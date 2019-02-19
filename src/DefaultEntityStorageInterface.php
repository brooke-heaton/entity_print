<?php

namespace Drupal\entity_email;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\entity_email\Entity\DefaultEntityInterface;

/**
 * Defines the storage handler class for News entities.
 *
 * This extends the base storage class, adding required special handling for
 * News entities.
 *
 * @ingroup entity_email
 */
interface DefaultEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of News revision IDs for a specific News.
   *
   * @param \Drupal\entity_email\Entity\DefaultEntityInterface $entity
   *   The News entity.
   *
   * @return int[]
   *   News revision IDs (in ascending order).
   */
  public function revisionIds(DefaultEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as News author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   News revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\entity_email\Entity\DefaultEntityInterface $entity
   *   The News entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(DefaultEntityInterface $entity);

  /**
   * Unsets the language for all News with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
