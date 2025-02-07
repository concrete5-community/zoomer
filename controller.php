<?php

namespace Concrete\Package\Zoomer;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends Package
{
    /**
     * @var string
     */
	protected $pkgHandle = 'zoomer';

	/**
	 * @var string
	 */
	protected $pkgVersion = '1.0.1';

	/**
	 * {@inheritdoc}
	 *
	 * @see \Concrete\Core\Package\Package::$appVersionRequired
	 */
	protected $appVersionRequired = '8.5.2';

	/**
	 * {@inheritdoc}
	 *
	 * @see \Concrete\Core\Package\Package::getPackageName()
	 */
	public function getPackageName()
	{
	    return t('Zoomer');
	}

	/**
	 * {@inheritdoc}
	 *
	 * @see \Concrete\Core\Package\Package::getPackageDescription()
	 */
	public function getPackageDescription()
	{
		return t('Add Zoomable Images to your site');
	}

	public function install()
	{
		$pkg = parent::install();
		BlockType::installBlockType('zoomer', $pkg);
	}
}
