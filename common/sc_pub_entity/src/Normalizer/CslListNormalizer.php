<?php

namespace Drupal\sc_pub_entity\Normalizer;

/**
 * Converts list objects to arrays.
 *
 * Ordinarily, this would be handled automatically by Serializer, but since
 * there is a TypedDataNormalizer and the Field class extends TypedData, any
 * Field will be handled by that Normalizer instead of being traversed. This
 * class ensures that TypedData classes that also implement ListInterface are
 * traversed instead of simply returning getValue().
 */
class CslListNormalizer extends CslNormalizerBase {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = 'Drupal\Core\TypedData\ListInterface';

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = array()) {
    if (count($object) === 1) {
      $attributes = $this->serializer->normalize($object[0], $format);
    }
    else {
      $attributes = array();
      foreach ($object as $fieldItem) {
        $attributes[] = $this->serializer->normalize($fieldItem, $format);
      }
    }

    return $attributes;
  }

}