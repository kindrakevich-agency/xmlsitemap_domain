<?php

namespace Drupal\xmlsitemap_domain\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\domain\DomainNegotiatorInterface;
use Psr\Log\LoggerInterface;

/**
 * Event subscriber to handle per-domain sitemap generation.
 */
class SitemapDomainSubscriber implements EventSubscriberInterface {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The domain negotiator.
   *
   * @var \Drupal\domain\DomainNegotiatorInterface|null
   */
  protected $domainNegotiator;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a new SitemapDomainSubscriber.
   */
  public function __construct(StateInterface $state, ModuleHandlerInterface $module_handler, LoggerInterface $logger, $domain_negotiator = NULL) {
    $this->state = $state;
    $this->moduleHandler = $module_handler;
    $this->logger = $logger;
    $this->domainNegotiator = $domain_negotiator;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Subscribe to kernel request event with high priority.
    $events[KernelEvents::REQUEST][] = ['onRequest', 100];
    return $events;
  }

  /**
   * Handles the request event.
   */
  public function onRequest(RequestEvent $event) {
    $request = $event->getRequest();
    $path = $request->getPathInfo();

    // Only process sitemap.xml requests.
    if ($path !== '/sitemap.xml') {
      return;
    }

    // Check if domain module is available.
    if (!$this->moduleHandler->moduleExists('domain') || !$this->domainNegotiator) {
      return;
    }

    // Get the active domain.
    $active_domain = $this->domainNegotiator->getActiveDomain();

    if (!$active_domain) {
      return;
    }

    $current_domain_id = $active_domain->id();

    // Check if this domain has sitemap enabled.
    $enabled_domains = $this->state->get('xmlsitemap_domain_enabled', []);
    if (!in_array($current_domain_id, $enabled_domains)) {
      return;
    }

    // Check if the cached sitemap is for a different domain.
    $last_domain = $this->state->get('xmlsitemap_domain_last_generated');

    if ($last_domain !== $current_domain_id) {
      $this->logger->notice('Clearing sitemap cache for domain switch: @old -> @new', [
        '@old' => $last_domain ?? 'none',
        '@new' => $current_domain_id,
      ]);

      // Delete cached sitemap files.
      $sitemap_dir = \Drupal::config('system.file')->get('default_scheme') . '://';
      $files_to_delete = ['sitemap.xml'];

      foreach ($files_to_delete as $filename) {
        $file_uri = $sitemap_dir . $filename;
        if (file_exists($file_uri)) {
          @unlink($file_uri);
        }
      }

      // Mark that we need regeneration.
      $this->state->set('xmlsitemap_regenerate_needed', TRUE);
      $this->state->set('xmlsitemap_domain_last_generated', $current_domain_id);
    }
  }

}
