# TODO - Tests Complets & Optimisations Bibliothèque

Statut : ⏳ En cours (par BLACKBOXAI)

## 📋 Étapes du Plan Approuvé

### 1. Création Tests Unit Manquants (Priorité Haute)
- [ ] `tests/Unit/ItemTypeTest.php` (factory, relations)
- [ ] `tests/Unit/TagTest.php` (attach/detach)
- [ ] `tests/Unit/ProfileTest.php` (pin, loans)
- [ ] `tests/Unit/WishlistTest.php` (add/remove)
- [ ] `tests/Unit/UserTest.php` (auth)
- [ ] `tests/Unit/ItemSuggestionTest.php`
- [ ] `tests/Unit/ItemReviewTest.php`
- [ ] Étendre ItemTest/LoanTest/CollectionTest

**Commande après :** `php artisan test tests/Unit`

### 2. Tests Feature CRUD (Ressources Filament)
- [ ] `tests/Feature/Resource/ItemResourceTest.php` (full CRUD)
- [ ] `tests/Feature/Resource/LoanResourceTest.php`
- [ ] `tests/Feature/Resource/CollectionResourceTest.php`
- [ ] `tests/Feature/Resource/ItemTypeResourceTest.php`
- [ ] `tests/Feature/Resource/TagResourceTest.php`
- [ ] `tests/Feature/Resource/ProfileResourceTest.php`
- [ ] `tests/Feature/Resource/WishlistResourceTest.php`
- [ ] `tests/Feature/Resource/UserResourceTest.php`
- [ ] `tests/Feature/Resource/ItemSuggestionResourceTest.php`

**Commande après :** `php artisan test tests/Feature/Resource`

### 3. Tests Avancés/E2E
- [ ] `tests/Feature/LoanWorkflowTest.php` (prêt->retard->retour)
- [ ] `tests/Feature/ImportTest.php` (OpenLibrary + doublons)
- [ ] `tests/Feature/CommandTest.php` (SendLoanReminders)

### 4. Optimisations (Migrations/Services)
- [ ] Migration indexes DB (`items.status`, `loans.due_at`)
- [ ] Eager loading dans tables Filament
- [ ] Config queues Redis
- [ ] Rules validation ISBN doublons
- [ ] SoftDeletes tous modèles
- [ ] Autres (API, Scout, Backups, PWA)

### 5. Validation Finale
- [ ] `php artisan test --coverage`
- [ ] Mise à jour README.md avec couverture tests

**Progression : 0/XX étapes complétées**

*Prochaines actions automatisées après chaque étape.*
