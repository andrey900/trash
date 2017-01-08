<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$arTemplateParameters['SHOW_UNIQUE_STAT'] = array(
	'NAME' => GetMessage("NSANDREY_TEXTCHECKER_POKAZYVATQ_PROCENT"),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'PARENT' => 'BASE'
);

$arTemplateParameters['SHOW_SPELLING_STAT'] = array(
	'NAME' => GetMessage("NSANDREY_TEXTCHECKER_POKAZYVATQ_KOLICESTV"),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'PARENT' => 'BASE'
);

$arTemplateParameters['SHOW_CHARS_WITH_SPACE_STAT'] = array(
	'NAME' => GetMessage("NSANDREY_TEXTCHECKER_POKAZYVATQ_KOLICESTV1"),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'PARENT' => 'BASE'
);

$arTemplateParameters['SHOW_CHARS_WITHOUT_SPACE_STAT'] = array(
	'NAME' => GetMessage("NSANDREY_TEXTCHECKER_POKAZYVATQ_KOLICESTV2"),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'PARENT' => 'BASE'
);

$arTemplateParameters['SHOW_WORDS_STAT'] = array(
	'NAME' => GetMessage("NSANDREY_TEXTCHECKER_POKAZYVATQ_KOLICESTV3"),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'PARENT' => 'BASE'
);

$arTemplateParameters['SHOW_SEO_WATER_STAT'] = array(
	'NAME' => GetMessage("NSANDREY_TEXTCHECKER_POKAZYVATQ_PROCENT1"),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'PARENT' => 'BASE'
);

$arTemplateParameters['SHOW_SEO_SPAM_STAT'] = array(
	'NAME' => GetMessage("NSANDREY_TEXTCHECKER_POKAZYVATQ_PROCENT2"),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'PARENT' => 'BASE'
);

$arTemplateParameters['SHOW_MIXED_WORDS_STAT'] = array(
	'NAME' => GetMessage("NSANDREY_TEXTCHECKER_POKAZYVATQ_KOLICESTV4"),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'PARENT' => 'BASE'
);