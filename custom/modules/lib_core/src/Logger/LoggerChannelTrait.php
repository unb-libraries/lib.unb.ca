<?php

namespace Drupal\lib_core\Logger;

/**
 * Provides methods to log to a logger channel.
 *
 * @package Drupal\lib_core\Logger
 */
trait LoggerChannelTrait {

  /**
   * The logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Get the logger.
   *
   * @return \Drupal\Core\Logger\LoggerChannelInterface
   *   A logger channel.
   */
  protected function logger() {
    return $this->logger;
  }

  /**
   * System is unusable.
   *
   * @param string $message
   *   The message.
   * @param mixed[] $context
   *   (optional) The message context.
   */
  public function emergency($message, array $context = []) {
    if ($this->logger()) {
      $this->logger()->emergency($message, $context);
    }
  }

  /**
   * Action must be taken immediately.
   *
   * Example: Entire website down, database unavailable, etc. This should
   * trigger the SMS alerts and wake you up.
   *
   * @param string $message
   *   The message.
   * @param mixed[] $context
   *   (optional) The message context.
   */
  public function alert($message, array $context = []) {
    if ($this->logger()) {
      $this->logger()->alert($message, $context);
    }
  }

  /**
   * Critical conditions.
   *
   * Example: Application component unavailable, unexpected exception.
   *
   * @param string $message
   *   The message.
   * @param mixed[] $context
   *   (optional) The message context.
   */
  public function critical($message, array $context = []) {
    if ($this->logger()) {
      $this->logger()->critical($message, $context);
    }
  }

  /**
   * Runtime errors.
   *
   * Errors that do not require immediate action but should typically
   * be logged and monitored.
   *
   * @param string $message
   *   The message.
   * @param mixed[] $context
   *   (optional) The message context.
   */
  public function error($message, array $context = []) {
    if ($this->logger()) {
      $this->logger()->error($message, $context);
    }
  }

  /**
   * Exceptional occurrences that are not errors.
   *
   * Example: Use of deprecated APIs, poor use of an API, undesirable things
   * that are not necessarily wrong.
   *
   * @param string $message
   *   The message.
   * @param mixed[] $context
   *   (optional) The message context.
   */
  public function warning($message, array $context = []) {
    if ($this->logger()) {
      $this->logger()->warning($message, $context);
    }
  }

  /**
   * Normal but significant events.
   *
   * @param string $message
   *   The message.
   * @param mixed[] $context
   *   (optional) The message context.
   */
  public function notice($message, array $context = []) {
    if ($this->logger()) {
      $this->logger()->notice($message, $context);
    }
  }

  /**
   * Interesting events.
   *
   * Example: User logs in, SQL logs.
   *
   * @param string $message
   *   The message.
   * @param mixed[] $context
   *   (optional) The message context.
   */
  public function info($message, array $context = []) {
    if ($this->logger()) {
      $this->logger()->info($message, $context);
    }
  }

  /**
   * Detailed debug information.
   *
   * @param string $message
   *   The message.
   * @param mixed[] $context
   *   (optional) The message context.
   */
  public function debug($message, array $context = []) {
    if ($this->logger()) {
      $this->logger()->debug($message, $context);
    }
  }

  /**
   * Logs with an arbitrary level.
   *
   * @param mixed $level
   *   The log level.
   * @param string $message
   *   The message.
   * @param mixed[] $context
   *   (optional) The message context.
   *
   * @throws \Psr\Log\InvalidArgumentException
   */
  public function log($level, $message, array $context = []) {
    if ($this->logger()) {
      $this->logger()->log($level, $message, $context);
    }
  }

}
