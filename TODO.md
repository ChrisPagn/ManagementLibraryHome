# Correction ISBN non reconnu (978-2-87907-816-8)

## Plan approuvé
- ✅ [AI] Créer ce fichier TODO.md
- ✅ [AI] Éditer app/Services/ImportService.php : utiliser fetchByIsbnWithFallback\n- ✅ [AI] Mettre à jour ce TODO.md avec ✅
- [User] Tester dans l'app : créer Item > ISBN 978-2-87907-816-8 > Importer
- [User] Vérifier logs si besoin : tail -f storage/logs/laravel.log

**Objectif** : Corriger import ISBN français via fallback Google Books quand OpenLibrary échoue.

**Fichiers** : ItemResource.php, ImportService.php

