<?php

$settings = array();

$tmp = array(


	//временные

//	'assets_path' => array(
//		'value' => '{base_path}skypenotify/assets/components/skypenotify/',
//		'xtype' => 'textfield',
//		'area' => 'skypenotify_temp',
//	),
//	'assets_url' => array(
//		'value' => '/skypenotify/assets/components/skypenotify/',
//		'xtype' => 'textfield',
//		'area' => 'skypenotify_temp',
//	),
//	'core_path' => array(
//		'value' => '{base_path}skypenotify/core/components/skypenotify/',
//		'xtype' => 'textfield',
//		'area' => 'skypenotify_temp',
//	),

	//временные

	/*
		'some_setting' => array(
			'xtype' => 'combo-boolean',
			'value' => true,
			'area' => 'skypenotify_main',
		),
		*/
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'skypenotify_' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
