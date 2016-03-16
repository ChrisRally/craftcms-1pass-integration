<?php


namespace Craft;


/**
 * 1Pass Integration Variable
 */
class OnePassVariable
{
	/**
	 * 1Pass variable
	 *
	 * @param
	 * @param string $action
	 * @param string $type
	 */
	public function getHTMLEmbedCode(EntryModel $entry, $summaryField = 'strap', $contentField = 'body', $charLimit = 200)
	{
		return craft()->onePass->getHTMLEmbedCode($entry, $summaryField, $contentField, $charLimit);
	}
}