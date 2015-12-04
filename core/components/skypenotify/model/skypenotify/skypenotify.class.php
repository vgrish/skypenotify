<?php

/**
 * The base class for skypenotify.
 */
class skypenotify
{
	/* @var modX $modx */
	public $modx;
	public $namespace = 'skypenotify';
	/* @var array The array of config */
	public $config = array();
	/** @var array $initialized */
	public $initialized = array();

	protected $username;
	protected $password;
	protected $registrationToken;
	protected $skypeToken;

	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array())
	{
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('skypenotify_core_path', $config, $this->modx->getOption('core_path') . 'components/skypenotify/');
		$assetsUrl = $this->modx->getOption('skypenotify_assets_url', $config, $this->modx->getOption('assets_url') . 'components/skypenotify/');
		$connectorUrl = $assetsUrl . 'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'imagesUrl' => $assetsUrl . 'images/',
			'connectorUrl' => $connectorUrl,

			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/',

		), $config);

		$this->modx->addPackage('skypenotify', $this->config['modelPath']);
		$this->modx->lexicon->load('skypenotify:default');
		$this->namespace = $this->getOption('namespace', $config, 'skypenotify');
	}

	/**
	 * @param $n
	 * @param array $p
	 */
	public function __call($n, array$p)
	{
		echo __METHOD__ . ' says: ' . $n;
	}

	/**
	 * @param $key
	 * @param array $config
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public function getOption($key, $config = array(), $default = null)
	{
		$option = $default;
		if (!empty($key) AND is_string($key)) {
			if ($config != null AND array_key_exists($key, $config)) {
				$option = $config[$key];
			} elseif (array_key_exists($key, $this->config)) {
				$option = $this->config[$key];
			} elseif (array_key_exists('{$this->namespace}_{$key}', $this->modx->config)) {
				$option = $this->modx->getOption('{$this->namespace}_{$key}');
			}
		}
		return $option;
	}

	/** @inheritdoc} */
	public function setConfig($config = array())
	{
		$this->config = array_merge(
			$this->config,
			array(
				'LOGIN_URL' => 'https://login.skype.com/login?client_id=578134&redirect_uri=https%3A%2F%2Fweb.skype.com',
				'ENDPOINTS_URL' => 'https://client-s.gateway.messenger.live.com/v1/users/ME/endpoints',
				'ENDPOINTS_SELF_URL' => 'https://client-s.gateway.messenger.live.com/v1/users/ME/endpoints/SELF/subscriptions',

				'PROFILE_URL' => 'https://api.skype.com/users/self/profile',
				'CONTACTS_URL' => 'https://contacts.skype.com/contacts/v1/users/[[+user]]/contacts?$filter=type%20eq%20%27skype%27&reason=default',
				'CONTACTS_ALL_URL' => 'https://contacts.skype.com/contacts/v1/users/[[+user]]/contacts?$filter=type%20eq%20%27skype%27%20or%20type%20eq%20%27msn%27%20or%20type%20eq%20%27pstn%27%20or%20type%20eq%20%27agent%27&reason=default',
				'USERS_PROFILE_URL' => 'https://api.skype.com/users/self/contacts/profiles',

				'SEND_MESSAGE_URL' => 'https://bay-client-s.gateway.messenger.live.com/v1/users/ME/conversations/[[+mode]]:[[+user]]/messages',
				'GET_MESSAGES_URL' => 'https://bay-client-s.gateway.messenger.live.com/v1/users/ME/conversations/[[+mode]]:[[+user]]/messages?startTime=0&pageSize=[[+size]]&view=msnp24Equivalent|supportsMessageProperties&targetType=Passport|Skype|Lync|Thread',

			),
			$config
		);
	}

	/**
	 * Initializes component into different contexts.
	 *
	 * @param string $ctx The context to load. Defaults to request.
	 * @param array $scriptProperties
	 *
	 * @return boolean
	 */
	public function initialize($ctx = 'request', $config = array())
	{
		$this->setConfig($config);
		$this->config['ctx'] = $ctx;
		if (!empty($this->initialized[$ctx])) {
			return true;
		}
		$this->initialized[$ctx] = true;
		return true;
	}

	/** @inheritdoc} */
	public function getToken($content, $name)
	{
		preg_match("#input[^>]+name=\"{$name}\"+.+value=\"(.*)\"#Usi", $content, $result);

		return isset($result[1]) ? $result[1] : false;
	}

	/** @inheritdoc} */
	protected function timestamp()
	{
		return str_replace('.', '', microtime(true));
	}

	/** @inheritdoc} */
	public function prepareUrl($url, array $opts = array())
	{
		if (isset($this->config[$url])) {
			$url = $this->config[$url];
		}
		$pls = $this->makePlaceholders($opts);
		$url = str_replace($pls['pl'], $pls['vl'], $url);
		return $url;
	}

	/** @inheritdoc} */
	public function request($url, $mode = 'GET', $post = null, $showHeaders = false, $suivre = true, array $headers = array(), $asis = false)
	{
		$post = !$asis ? json_encode($post, JSON_FORCE_OBJECT) : $post;

		if (isset($this->registrationToken) AND isset($this->skypeToken)) {
			$headers = array_merge($headers, array(
				"X-Skypetoken: {$this->skypeToken}",
				"RegistrationToken: registrationToken={$this->registrationToken}",
				"Content-Length: " . strlen($post),
				//"Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n"
				"Content-Type: application/x-www-form-urlencoded"
			));
		}

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, ($url));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $mode);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HEADER, $showHeaders);
		@curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $suivre);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);

		$response = curl_exec($curl);

		if (curl_errno($curl)) {
			throw new ErrorException(curl_error($curl), curl_errno($curl));
		}
		curl_close($curl);

		return $response;
	}

	/** @inheritdoc} */
	public function login($skypeToken)
	{
		$this->skypeToken = $skypeToken;

		$auth = $this->request(
			$this->prepareUrl('ENDPOINTS_URL'),
			'POST',
			array(),
			true,
			true,
			array("Authentication: skypetoken=$skypeToken")
		);

		preg_match('`registrationToken=(.+);`isU', $auth, $registrationToken);
		if (!isset($registrationToken[1])) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, "[skypenotify] Authentication failed. Line: " . __LINE__);
			return false;
		}
		$this->registrationToken = $registrationToken[1];

		$options = array(
			'cache_key' => 'tokens/' . $this->username,
			'cacheTime' => time() + 86400,
		);
		$this->setCache(array($this->skypeToken, $this->registrationToken), $options);
		$this->request(
			$this->prepareUrl('ENDPOINTS_SELF_URL'),
			'POST',
			array(
				'channelType' => 'httpLongPoll',
				'template' => 'raw',
				'interestedResources' => array(
					'/v1/users/ME/conversations/ALL/properties',
					'/v1/users/ME/conversations/ALL/messages',
					'/v1/users/ME/contacts/ALL',
					'/v1/threads/ALL',
				)
			)
		);

		return true;
	}

	/** @inheritdoc} */
	public function connect($username, $password)
	{
		$this->username = $username;
		$this->password = $password;

		$options = array(
			'cache_key' => 'tokens/' . $this->username,
			'cacheTime' => 0,
		);

		if (!$tokens = $this->getCache($options)) {
			$auth = $this->request(
				$this->prepareUrl('LOGIN_URL'),
				'GET',
				null,
				true,
				true,
				array(),
				false
			);

			$tokens = Array(
				'username' => $username,
				'password' => $password,
				'pie' => $this->getToken($auth, 'pie'),
				'etm' => $this->getToken($auth, 'etm'),
				'js_time' => $this->getToken($auth, 'js_time'),
				'timezone_field' => '+02|00',
				'client_id' => 578134,
				'redirect_uri' => 'http://request.skype.com/'
			);

			$auth = $this->request(
				$this->prepareUrl('LOGIN_URL'),
				'POST',
				$tokens,
				false,
				true,
				array(),
				true
			);

			if ($skypeToken = $this->getToken($auth, 'skypetoken')) {
				$this->login($skypeToken);
			} else {
				$this->modx->log(modX::LOG_LEVEL_ERROR, "[skypenotify] Authentication failed. " . __LINE__);
				return false;
			}
		} else {
			$this->skypeToken = $tokens[0];
			$this->registrationToken = $tokens[1];
		}

		return true;
	}

	/** @inheritdoc} */
	public function logout()
	{
		$options = array(
			'cache_key' => 'tokens/',
			'cacheTime' => 0,
		);
		$this->clearCache($options);

		return $this->registrationToken = null;
	}

	/** @inheritdoc} */
	public function getProfile()
	{
		$req = $this->request(
			$this->prepareUrl('PROFILE_URL')
		);
		$data = json_decode($req, true);

		return !empty($data) ? $data : false;
	}

	/** @inheritdoc} */
	public function getContacts()
	{
		$req = $this->request(
			$this->prepareUrl('CONTACTS_URL', array('user' => $this->username))
		);
		$contacts = json_decode($req, true);

		return isset($contacts['contacts']) ? $contacts['contacts'] : false;
	}

	/** @inheritdoc} */
	public function getContactsAll()
	{
		$req = $this->request(
			$this->prepareUrl('CONTACTS_ALL_URL', array('user' => $this->username))
		);
		$contacts = json_decode($req, true);

		return isset($contacts['contacts']) ? $contacts['contacts'] : false;
	}

	/** @inheritdoc} */
	public function getUsersProfile($users = '')
	{
		if (!is_array($users)) {
			$users = explode(',', $users);
		}
		$contacts = '';
		foreach ($users as $out) {
			$contacts .= "contacts[]=$out&";
		}
		$req = $this->request(
			$this->prepareUrl('USERS_PROFILE_URL'),
			'POST',
			$contacts,
			false,
			true,
			array(),
			true
		);
		$data = json_decode($req, true);

		return !empty($data) ? $data : false;
	}

	/** @inheritdoc} */
	public function URLtoUser($url)
	{
		return str_replace(
			'https://db3-client-s.gateway.messenger.live.com/v1/users/ME/contacts/',
			'',
			str_replace('8:', '', str_replace('19:', '', $url)
			)
		);
	}

	/** @inheritdoc} */
	public function sendMessage($user, $message)
	{
		$opts = array();
		$opts['user'] = $this->URLtoUser($user);
		$opts['mode'] = (strstr($opts['user'], '@thread.skype') ? 19 : 8);
		$ms = $this->timestamp();
		$req = json_decode($this->request(
			$this->prepareUrl(
				'SEND_MESSAGE_URL',
				$opts
			),
			'POST',
			array(
				'content' => $message,
				'contenttype' => 'text',
				'messagetype' => 'RichText',
				'clientmessageid' => $ms
			)
		), true);

		return isset($req['OriginalArrivalTime']) ? $ms : false;
	}

	/** @inheritdoc} */
	public function getMessages($user, $size = 100)
	{
		$opts = array();
		$opts['user'] = $this->URLtoUser($user);
		$opts['mode'] = (strstr($opts['user'], '@thread.skype') ? 19 : 8);
		$opts['size'] = ($size > 199 OR $size < 1) ? 199 : $size;
		$req = json_decode($this->request(
			$this->prepareUrl(
				'GET_MESSAGES_URL',
				$opts
			),
			'GET',
			null,
			false,
			true,
			array(),
			true
		), true);

		return (!isset($req['message']) ? $req : false);
	}


	/** @inheritdoc} */
//	public function createGroup($users) {
//		if (!is_array($users)) {
//			$users = explode(',', $users);
//		}
//		foreach ($users as $user) {
//			if (empty($user)) {
//				continue;
//			}
//			$members['members'][] = Array('id' => '8:'.$this->URLtoUser($user), 'role' => 'User');
//		}
//		$members["members"][] = Array('id' => '8:'.$this->username, 'role' => 'Admin');
//
//		$req = $this->request(
//			"https://client-s.gateway.messenger.live.com/v1/threads",
//			'POST',
//			json_encode($members),
//			true,
//			true,
//			array(),
//			true
//		);
//
//		$this->modx->log(modX::LOG_LEVEL_ERROR, print_r($req,1));
//
//		return true;
//	}


	/**
	 * Sets data to cache
	 *
	 * @param mixed $data
	 * @param mixed $options
	 *
	 * @return string $cacheKey
	 */
	public function setCache($data = array(), $options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		if (!empty($cacheKey) AND !empty($cacheOptions) AND $this->modx->getCacheManager()) {
			$this->modx->cacheManager->set(
				$cacheKey,
				$data,
				$cacheOptions[xPDO::OPT_CACHE_EXPIRES],
				$cacheOptions
			);
		}
		return $cacheKey;
	}

	/**
	 * Returns data from cache
	 *
	 * @param mixed $options
	 *
	 * @return mixed
	 */
	public function getCache($options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		$cached = '';
		if (!empty($cacheOptions) AND !empty($cacheKey) AND $this->modx->getCacheManager()) {
			$cached = $this->modx->cacheManager->get($cacheKey, $cacheOptions);
		}
		return $cached;
	}


	/**
	 * @param array $options
	 *
	 * @return bool
	 */
	public function clearCache($options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		$cacheOptions['cache_key'] .= $cacheKey;
		if (!empty($cacheOptions) AND $this->modx->getCacheManager()) {
			return $this->modx->cacheManager->clean($cacheOptions);
		}
		return false;
	}

	/**
	 * Returns array with options for cache
	 *
	 * @param $options
	 *
	 * @return array
	 */
	public function getCacheOptions($options = array())
	{
		if (empty($options)) {
			$options = $this->config;
		}
		$cacheOptions = array(
			xPDO::OPT_CACHE_KEY => empty($options['cache_key'])
				? 'default' : 'default/' . $this->namespace . '/',
			xPDO::OPT_CACHE_HANDLER => !empty($options['cache_handler'])
				? $options['cache_handler'] : $this->modx->getOption('cache_resource_handler', null, 'xPDOFileCache'),
			xPDO::OPT_CACHE_EXPIRES => $options['cacheTime'] !== ''
				? (integer)$options['cacheTime'] : (integer)$this->modx->getOption('cache_resource_expires', null, 0),
		);
		return $cacheOptions;
	}

	/**
	 * Returns key for cache of specified options
	 *
	 * @var mixed $options
	 * @return bool|string
	 */
	public function getCacheKey($options = array())
	{
		if (empty($options)) {
			$options = $this->config;
		}
		if (!empty($options['cache_key'])) {
			return $options['cache_key'];
		}
		$key = !empty($this->modx->resource) ? $this->modx->resource->getCacheKey() : '';
		return $key . '/' . sha1(serialize($options));
	}

	/**
	 * return lexicon message if possibly
	 *
	 * @param string $message
	 *
	 * @return string $message
	 */
	public function lexicon($message, $placeholders = array())
	{
		$key = '';
		if ($this->modx->lexicon->exists($message)) {
			$key = $message;
		} elseif ($this->modx->lexicon->exists($this->namespace . '_' . $message)) {
			$key = $this->namespace . '_' . $message;
		}
		if ($key !== '') {
			$message = $this->modx->lexicon->process($key, $placeholders);
		}
		return $message;
	}

	/**
	 * Transform array to placeholders
	 *
	 * from https://github.com/bezumkin/pdoTools/blob/56f66c3a18dfb894e3a4aafdc1a4e36973e14ac3/core/components/pdotools/model/pdotools/pdotools.class.php#L282
	 *
	 * @param array $array
	 * @param string $plPrefix
	 * @param string $prefix
	 * @param string $suffix
	 * @param bool $uncacheable
	 *
	 * @return array
	 */
	public function makePlaceholders(array $array = array(), $plPrefix = '', $prefix = '[[+', $suffix = ']]', $uncacheable = true)
	{
		$result = array('pl' => array(), 'vl' => array());
		$uncached_prefix = str_replace('[[', '[[!', $prefix);
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$result = array_merge_recursive($result, $this->makePlaceholders($v, $plPrefix . $k . '.', $prefix, $suffix, $uncacheable));
			} else {
				$pl = $plPrefix . $k;
				$result['pl'][$pl] = $prefix . $pl . $suffix;
				$result['vl'][$pl] = $v;
				if ($uncacheable) {
					$result['pl']['!' . $pl] = $uncached_prefix . $pl . $suffix;
					$result['vl']['!' . $pl] = $v;
				}
			}
		}
		return $result;
	}

}