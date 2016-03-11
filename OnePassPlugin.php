<?php


namespace Craft;


/**
 * 1Pass Integration Plugin
 */
/**
 * Class OnePassPlugin
 * @package Craft
 */
class OnePassPlugin extends BasePlugin
{
	/**
	 * Plugin renaming in CP
	 *
	 * @return String
	 */
	public function getName()
    {
	    $pluginName	= Craft::t('1Pass');
	    $pluginNameOverride	= $this->getSettings()->pluginNameOverride;


	    return ($pluginNameOverride) ? $pluginNameOverride : $pluginName;
    }


	/**
	 * Plugin version
	 *
	 * @return string
	 */
	public function getVersion()
    {
        return '0.0.1';
    }


	/**
	 * Plugin developer
	 *
	 * @return String
	 */
	public function getDeveloper ()
	{
		return 'Rally Agency Ltd';
	}


	/**
	 * Plugin URL
	 *
	 * @return String
	 */
	public function getDeveloperUrl ()
	{
		return 'http://www.rallyagency.co.uk';
	}


	/**
	 * 1Pass integration documentation
	 *
	 * @return string
	 */
	public function getDocumentationUrl()
	{
		return 'http://onepass-reference.herokuapp.com';
	}


	/**
	 * Plugin settings
	 *
	 * @return array
	 */
	protected function defineSettings()
	{
		return [
			'pluginNameOverride' => [
				AttributeType::String
			],
			'apiPublishableKey' => [
                AttributeType::String,
                'required' => true
            ],
            'apiSecretKey' => [
                AttributeType::String,
                'required' => true
            ],
			'apiMode' => [
				AttributeType::String,
				'required' => true
			],
			'section' => [
				AttributeType::Number,
				'required' => true
			],
			'contentField' => [
				AttributeType::String,
				'required' => true
			],
			'paymentRequiredField' => [
				AttributeType::String,
				'required' => true
			]
		];
	}


	/**
	 * Gets the Fields assigned to a particular Section's FieldLayout
	 * @param SectionModel $section Section to work with
	 * @return array Array of Fields for the Section
	 */
	private function getSectionLayoutFields($section)
	{
		$fields = [];


		foreach ($section->getEntryTypes() as $entryType)
		{
			foreach ($entryType->getFieldLayout()->getFields() as $field)
			{
				$fieldData = $field->getField();


				$fields[] = [
					'label' => $fieldData->name,
					'value' => $fieldData->handle
				];
			}
		}


		return $fields;
	}


	/**
	 * Array used to set initial empty option of a settings dropdown
	 * @return array
	 */
	private function emptyOptions()
	{
		return [
			[
				'label' => 'Please select',
				'value' => ''
			]
		];
	}


	/**
	 * Plugin settings HTML
	 *
	 * @return string
	 */
	public function getSettingsHtml()
	{
		$apiModes = [
			[
				'label' => 'Demo',
				'value' => 'demo'
			], [
				'label' => 'Live',
				'value' => 'live'
			]
		];


		$sections = $this->emptyOptions();


		foreach (craft()->sections->getAllSections() as $section)
		{
			$sections[] = [
				'id' => $section->id,
				'label' => $section->name,
				'value' => $section->id,
				'fields' => $this->getSectionLayoutFields($section)
			];
		}


		return craft()->templates->render('onepass/settings', [
			'settings' => $this->getSettings(),
			'options' => $this->emptyOptions(),
			'apiModes' => $apiModes,
			'sections' => $sections
		]);
	}


	/**
	 * Plugin initialisation
	 * Call Composer autoloader
	 *
	 */
	public function init()
	{
		$this->initializeAutoloader();
	}


	/**
	 * Initialise Composer Autoloader
	 * Bring in 1Pass common package
	 * https://github.com/1Pass/1pass-common
	 *
	 */
	protected function initializeAutoloader()
	{
		require_once __DIR__ . '/vendor/autoload.php';
	}
}