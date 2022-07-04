<?php

namespace Drupal\portolan_sync\Synchronization;

/**
 * Provides data residing in a MARC file.
 *
 * @package Drupal\portolan_sync\Synchronization
 */
class MarcFileProvider implements DataProviderInterface {

  /**
   * The file transfer service.
   *
   * @var \Drupal\portolan_sync\Synchronization\FileTransferInterface
   */
  protected $fileTransfer;

  /**
   * The source file location.
   *
   * @var string
   */
  protected $source;

  /**
   * Get the file transfer service.
   *
   * @return \Drupal\portolan_sync\Synchronization\FileTransferInterface
   *   A file transfer object.
   */
  protected function fileTransfer() {
    return $this->fileTransfer;
  }

  /**
   * Get the source file location.
   *
   * @return string
   *   A file path.
   */
  protected function getSource() {
    return $this->source;
  }

  /**
   * Create a new MarcFileProvider instance.
   *
   * @param \Drupal\portolan_sync\Synchronization\FileTransferInterface $file_transfer
   *   A file transfer object.
   * @param string $source
   *   A file path.
   */
  public function __construct(FileTransferInterface $file_transfer, $source) {
    $this->fileTransfer = $file_transfer;
    $this->source = $source;
  }

  /**
   * {@inheritDoc}
   */
  public function getData() {
    $from = $this->getSource();
    $marc_file_path = $this->fileTransfer()->copy($from, '/tmp/portolan.mrc');
    return new \File_MARC($marc_file_path);
  }

}
