<?php
/**
 * User: Katzen48
 * Date: 03.10.2020
 * Time: 13:56
 */

namespace Katzen48\LaravelBunnyCdn\Filesystem;

use BunnyCDN\Storage\Exceptions\BunnyCDNStorageException;
use League\Flysystem\Config;
use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNAdapter as Adapter;
use PlatformCommunity\Flysystem\BunnyCDN\Util;

class BunnyCDNAdapter extends Adapter
{
	public function __construct(BunnyCDNStorage $bunnyCDNStorage)
	{
		parent::__construct($bunnyCDNStorage);
	}

	public function writeStream($path, $resource, Config $config)
	{
		try {
			$fileStream = $resource;
			
			if ($fileStream === false) {
				throw new BunnyCDNStorageException('The resource could not be opened.');
			}
			$dataLength = fstat($resource)['size'];
			$storage = $this->getStorage();
			$normalizedPath = $this->fullPath($path);

			return $storage->sendHttpRequest($normalizedPath, 'PUT', $fileStream, $dataLength);
		} catch (BunnyCDNStorageException $e) {
			report($e);

			return false;
		}
	}

	/**
	 * @param $path
	 *
	 * @return string
	 */
	private function fullPath($path): string
	{
		return '/' . $this->applyPathPrefix('/' . Util::normalizePath($path));
	}

	private function getStorage(): BunnyCDNStorage
	{
		return $this->bunnyCDNStorage;
	}
}
