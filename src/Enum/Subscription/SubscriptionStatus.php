<?php

namespace App\Enum\Subscription;

/**
 * Représente l'état métier d’un abonnement dans le système.
 *
 * ⚠️ Ces statuts sont indépendants du provider (Apple / Google).
 * Ils représentent l'état logique interne du SaaS.
 *
 * IMPORTANT :
 * L'accès utilisateur dépend à la fois du status ET de expiresAt.
 */
enum SubscriptionStatus: string
{
  /**
   * État indéfini ou non initialisé.
   * Utilisé comme fallback ou valeur par défaut.
   *
   * Accès utilisateur : ❌ Non
   */
  case SUBSCRIPTION_STATE_UNSPECIFIED = 'SUBSCRIPTION_STATE_UNSPECIFIED';

  /**
   * Achat initié mais non confirmé.
   * Exemple : paiement en attente de validation.
   *
   * Accès utilisateur : ❌ Non
   */
  case SUBSCRIPTION_STATE_PENDING = 'SUBSCRIPTION_STATE_PENDING';

  /**
   * Abonnement actif.
   * Paiement validé et date d'expiration future.
   *
   * Accès utilisateur : ✅ Oui
   */
  case SUBSCRIPTION_STATE_ACTIVE = 'SUBSCRIPTION_STATE_ACTIVE';

  /**
   * Abonnement temporairement suspendu (Google uniquement).
   * L’utilisateur a volontairement mis en pause son abonnement.
   *
   * Accès utilisateur : ❌ Non pendant la pause
   */
  case SUBSCRIPTION_STATE_PAUSED = 'SUBSCRIPTION_STATE_PAUSED';

  /**
   * Paiement échoué mais période de grâce active.
   * Exemple : carte expirée, délai de régularisation.
   *
   * Accès utilisateur : ✅ Oui (temporairement)
   */
  case SUBSCRIPTION_STATE_IN_GRACE_PERIOD = 'SUBSCRIPTION_STATE_IN_GRACE_PERIOD';

  /**
   * Paiement échoué sans période de grâce active.
   * En attente de régularisation (billing retry / account hold).
   *
   * Accès utilisateur : ❌ Non
   */
  case SUBSCRIPTION_STATE_ON_HOLD = 'SUBSCRIPTION_STATE_ON_HOLD';

  /**
   * Renouvellement automatique désactivé par l'utilisateur.
   * L’abonnement reste actif jusqu’à sa date d’expiration.
   *
   * ⚠️ Ce n’est PAS un remboursement.
   *
   * Accès utilisateur : ✅ Oui (jusqu'à expiresAt)
   */
  case SUBSCRIPTION_STATE_CANCELED = 'SUBSCRIPTION_STATE_CANCELED';

  /**
   * Abonnement expiré naturellement.
   * La date d'expiration est passée.
   *
   * Accès utilisateur : ❌ Non
   */
  case SUBSCRIPTION_STATE_EXPIRED = 'SUBSCRIPTION_STATE_EXPIRED';

  /**
   * Achat annulé avant confirmation.
   * Exemple : utilisateur ferme la popup paiement.
   *
   * Accès utilisateur : ❌ Non
   */
  case SUBSCRIPTION_STATE_PENDING_PURCHASE_CANCELED = 'SUBSCRIPTION_STATE_PENDING_PURCHASE_CANCELED';

  /**
   * Abonnement révoqué par le store.
   * Exemple :
   * - Remboursement
   * - Chargeback
   * - Fraude
   *
   * ⚠️ Différent de CANCELED.
   * L'accès doit être coupé immédiatement.
   *
   * Accès utilisateur : ❌ Non immédiatement
   */
  case SUBSCRIPTION_STATE_REVOKED = 'SUBSCRIPTION_STATE_REVOKED';
}
