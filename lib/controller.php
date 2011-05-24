<?php

class WikiController {
	// Wiki default configuration. All overridableindex
	protected $config = array(
		'docDir'      => '/tmp/',
		'defaultPage' => 'index',
		'newPageText' => 'Тази страница все още не съществува.<br>Натиснете "Редактирай", за да я създадете.',
		'markdownExt' => 'markdown'
	);

	// An instance of the Markdown parser
	protected $parser;
	protected $baseUrl;

	public function __construct($config=false) {
		$this->initWiki();
		if ($config) {
			$this->setConfig($config);
		}
	}

	protected function initWiki() {
		$baseDir = dirname(__FILE__) . '/';

		// Including the markdown parser
		require_once $baseDir . 'markdown.php';
	}

	public function wikiLink($link) {
		global $docIndex;

		$isNew = false;
		$wikiUrl = $link;

		if (preg_match('/^\/?([a-z0-9-]+(\/[a-z0-9-]+)*)$/i', $link, $matches)) {
			$wikiUrl = "{$this->baseUrl}{$matches[1]}";
			$isNew = !$this->isMarkdownFile($link);
		} elseif ($link=='/') {
			$wikiUrl = "{$this->baseUrl}{$this->config['defaultPage']}";
			$isNew = !$this->isMarkdownFile($this->config['defaultPage']);
		}

		return array($isNew, $wikiUrl);
	}

	public function isMarkdownFile($link) {
		$filename = "{$this->config['docDir']}{$link}.{$this->config['markdownExt']}";
		return file_exists($filename);
	}

	public function setConfig($config) {
		$this->config = array_merge($this->config, $config);
	}

	public function handleRequest($request=false, $server=false) {
		$action           = $this->parseRequest($request, $server);
		$action->model    = $this->getModelData($action);

		// If this is a new file, switch to edit mode
		if ($action->model->updated==0 && $action->action=='display') {
			$action->action = 'edit';
		}

		$action->response = $this->doAction($action);
		$output           = $this->renderResponse($action);
	}

	##
	## Methods handling each action
	##

	public function doAction($action) {

		switch($action->action) {
			case 'UNKNOWN': # Default to display
			case 'display':
				$response = $this->doDisplay($action);
				break;
			case 'edit':
				$response = $this->doEdit($action);
				break;
			case 'delete':
				@unlink($action->model->file);
				$this->redirectTo($this->baseUrl . $action->page, array('success' => "Страницата '{$action->page}' е изтрита успешно."));
				break;
			case 'preview':
				$response = $this->doPreview($action);
				break;
			case 'save':
				$response = $this->doSave($action);
				break;
			case 'history':
			case 'admin':
			case 'browse':
			default:
				$response = array(
					'messages' => array(
						"Action {$action->action} not implemented."
					)
				);
				print_r($action);
				break;
		}

		return $response;
	}

	protected function doDisplay($action) {
		$response = array(
			'title'    => $this->pageName($action->page),
			'content'  => $this->renderDocument($action),
			'editForm' => '',
			'options'  => array(
				'Редактирай' => "{$action->base}{$action->page}?action=edit&id={$action->page}",
				'Изтрий'     => "{$action->base}{$action->page}?action=delete&confirm=true&id={$action->page}",
			),
			'related'  => ''
		);

		return $response;
	}

	protected function doEdit($action) {
		$response = array(
			'title'    => "Редактиране: " . $this->pageName($action->page),
			'content'  => '',
			'editForm' => $this->renderEditForm($action),
			'options'  => array(
				'Отказ' => "{$action->base}{$action->page}"
			),
			'related'  => ''
		);

		return $response;
	}

	protected function doPreview($action) {
		$response = array(
			'title'    => "Преглед: " . $this->pageName($action->page),
			'content'  => '<div class="preview">' . $this->renderPreviewDocument($action) . '</div>',
			'editForm' => $this->renderEditForm($action),
			'options'  => array(
				'Отказ' => "{$action->base}{$action->page}"
			),
			'related'  => ''
		);

		return $response;
	}

	protected function doSave($action) {
		// TODO: Implement some sort of versioning
		if (empty($action->model)) {
			// This is a new file
			$this->addMessage('notice', 'Създаване на нова страница...');
		} elseif ($action->model->updated == $action->post->updated) {
			// Check there isn't an editing conflict
			$action->model->content = $action->post->text;
			$this->setModelData($action->model);

			$this->redirectTo("{$action->base}{$action->page}", array('success' => 'Страницата е обновена успешно.'));
		} else {
			$this->addMessage('error', 'Внимание: Възникнал е конфликт при опита за редакция на страницата.');
		}

		return $this->doDisplay($action);
	}

	##
	## Methods dealing with the model (plain old file system)
	##

	protected function getModelData($action) {
		$data = (object) NULL;

		$data->file    = $this->getFilename($action->page);
		$data->content = $this->getContent($data->file);
		$data->updated = $this->getLastUpdated($data->file);

		return $data;
	}

	protected function setModelData($model) {
		$directory = dirname($model->file);
		if (!file_exists($directory)) {
			mkdir($directory, 0777, true);
		} elseif (!is_dir($directory)) {
			$this->addMessage('error', "Грешка: неуспешен опит за създаване на {$model->file}");
			return;
		}

		file_put_contents($model->file, $model->content);
	}

	##
	## Methods for parsing the incoming request
	##

	public function parseRequest($request=false, $server=false) {
		$action = (object) NULL;

		if (!$request) { $request = $_REQUEST; }
		if (!$server)  { $server  = $_SERVER;  }

		//echo "Request: "; print_r($request);
		//echo "Server : "; print_r($server);

		$action->method = $this->getMethod($request, $server);
		$action->page   = $this->getPage($request, $server);
		$action->action = $this->getAction($request, $server);
		$action->base   = $this->getBaseUrl($request, $server);

		if ($action->method=='POST') {
			$action->post = $this->getPostDetails($request, $server);
		}

		// Take a copy of the action base for the wikiLink function
		$this->baseUrl = $action->base;

		return $action;
	}

	protected function getFilename($page) {
		return "{$this->config['docDir']}{$page}.{$this->config['markdownExt']}";
	}

	protected function getContent($filename) {
		if (file_exists($filename)) {
			return file_get_contents($filename);
		}
		$this->addMessage('info', $this->config['newPageText']);
	}

	protected function getLastUpdated($filename) {
		if (file_exists($filename)) {
			return filectime($filename);
		}
		return 0;
	}

	protected function getMethod($request, $server) {
		if (!empty($server['REQUEST_METHOD'])) {
			return $server['REQUEST_METHOD'];
		}
		return 'UNKNOWN';
	}

	protected function getPage($request, $server) {
		$page = '';

		// Determine the page name
		if (!empty($server['PATH_INFO'])) {
			//echo "Path info detected\n";
			// If we are using PATH_INFO then that's the page name
			$page = substr($server['PATH_INFO'], 1);

		} elseif (!empty($request['id'])) {
			$page = $request['id'];

		} else {
			// TODO: Keep checking
			//echo "WARN: Could not find a pagename\n";
		}

		// Check whether a default Page is being requested
		if ($page=='' || preg_match('/\/$/', $page)) {
			$page .= $this->config['defaultPage'];
		}

		return $page;
	}

	protected function pageName($page) {
		return ucwords(preg_replace('/[_\-\.\s]+/', ' ', $page));
	}

	protected function getAction($request, $server) {
		if ($server['REQUEST_METHOD']=='POST') {
			if (!empty($request['preview'])) {
				return 'preview';
			} elseif (!empty($request['save'])) {
				return 'save';
			}
		} elseif (!empty($request['action'])) {
			return $request['action'];
		} elseif (!empty($server['PATH_INFO'])) {
			return 'display';
		}

		// TODO: handle version history etc.

		return 'UNKNOWN';
	}

	protected function getBaseUrl($request, $server) {
		if (!empty($this->config['baseUrl'])) {
			return $this->config['baseUrl'];
		}

		$scriptName = $server['SCRIPT_NAME'];
		$requestUrl = $server['REQUEST_URI'];
		$phpSelf    = $server['PHP_SELF'];

		if ($requestUrl==$scriptName) {
			// PATH_INFO based
		} elseif(strpos($requestUrl, $scriptName)===0) {
			// Query string based
		} else {
			// Maybe mod_rewrite based?
			// Perhaps we need a config entry here
		}

		return dirname($server['SCRIPT_NAME']) . '/';
	}

	protected function getPostDetails($request, $server) {
		$post = (object) NULL;
		$post->text    = stripslashes($request['text']);
		$post->updated = $request['updated'];
		return $post;
	}

	protected function addMessage($type, $message) {
		if (!isset($_SESSION['flashes']) || !is_array($_SESSION['flashes'])) {
			$_SESSION['flashes'] = array();
		}
		if (!isset($_SESSION['flashes'][$type])) {
			$_SESSION['flashes'][$type] = array();
		}
		$_SESSION['flashes'][$type][] = $message;
	}

	protected function clearMessages() {
		unset($_SESSION['flashes']);
	}

	protected function renderMessages() {
		$html = '';
		if (!empty($_SESSION['flashes']) && is_array($_SESSION['flashes'])) {
			$html .= '<div class="messages">';
			foreach ($_SESSION['flashes'] as $type => $messages) {
				$html .= '<div class="' . $type . '">' . join('<br>', $messages) . '</div>';
			}
			$html .= '</div>';
		}

		$this->clearMessages();
		return $html;
	}

	protected function redirectTo($url, $messages = null) {
		if ($messages !== null && is_array($messages)) {
			$this->clearMessages();
			foreach ($messages as $type => $message) {
				$this->addMessage($type, $message);
			}
		}

		header("Location: $url");
		exit;
	}

	/*********

		RESPONSE RENDERERS

	*********/

	public function renderResponse($action) {
		$response = $action->response;
		$footer   = array();

		// prepare response options (page actions)
		if (!empty($response['options'])) {
			$footer[] = '<ul>';
			$items = count($response['options']);
			$index = 1;
			foreach ($response['options'] as $label => $link) {
				$classes = $index++ == $items ? 'last' : '';
				$confirmation = '';

				// add the link's params as CSS classes
				parse_str(parse_url($link, PHP_URL_QUERY), $params);
				foreach ($params as $key => $value) {
					$classes .= " $key-$value";
					if ($key == 'confirm' && !empty($value)) {
						$confirmation = 'onclick="return confirm(\'Сигурни ли сте?\');"';
					}
				}
				$classes = trim($classes);

				$link = htmlspecialchars($link);
				$footer[] = <<<HTML
					<li class="$classes"><a href="$link" {$confirmation}>$label</a></li>
HTML;
			}
			$footer[] = '</ul>';
		}
		$response['footer'] = implode("\n", $footer);

		// simple navigation
		$pages = array();
		$page_files = glob("{$this->config['docDir']}*.{$this->config['markdownExt']}");
		sort($page_files, SORT_STRING);
		foreach ($page_files as $file) {
			if (preg_match("/^(.*)\\.{$this->config['markdownExt']}$/", basename($file), $matches)) {
				$page  = $matches[1];
				$link  = '<li ' . ($page == $action->page ? 'class="active"' : '') . '>';
				$link .= '<a href="' . $this->baseUrl . $page . '">' . htmlspecialchars($this->pageName($page)) . '</a>';
				$link .= '</li>';
				$pages[] = $link;
			}
		}
		$response['navigation'] = '<ul>' . join('', $pages) . '</ul>';

		// the header
		include(dirname(__FILE__) . '/header.php');

		// render any flash messages
		$response['messages'] = $this->renderMessages();

		// the content
		echo <<<PAGE
<div id="page">
	<div id="nav" class="boxed">
		{$response['navigation']}
	</div>
	{$response['messages']}
	<div id="content">
		{$response['content']}
		{$response['editForm']}
	</div>
	<div id="related">
		{$response['related']}
	</div>
	<div id="actions" class="boxed">
		{$response['footer']}
	</div>
</div>
PAGE;

		// the footer
		include(dirname(__FILE__) . '/footer.php');
	}

	protected function renderDocument($action) {
		return Markdown(
			$action->model->content,
			array($this, 'wikiLink')
		);
	}

	protected function renderPreviewDocument($action) {
		return Markdown(
			$action->post->text,
			array($this, 'wikiLink')
		);
	}

	protected function renderEditForm($action) {
		if (!empty($action->post)) {
			$form = array(
				'raw'     => $action->post->text,
				'updated' => $action->post->updated
			);
		} else {
			$form = array(
				'raw'     => $action->model->content,
				'updated' => $action->model->updated
			);
		}

		return <<<HTML
<form action="{$action->base}{$action->page}" method="post" id="edit-page-form">
	<fieldset>
		<legend>Редакция на страница</legend>
		<label for="text">Съдържание:</label><br>
		<textarea cols="80" rows="80" name="text" id="text">{$form['raw']}</textarea>
		<br>

		<input type="submit" name="preview" value="Преглед">
		<input type="submit" name="save" value="Запази">
		<input type="hidden" name="updated" value="{$form['updated']}">
	</fieldset>
</form>
HTML;

	}


}

# Process the request and render a response
session_start();

$wiki = new WikiController($config);
$wiki->handleRequest();

?>
