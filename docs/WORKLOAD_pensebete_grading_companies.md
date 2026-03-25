# WORKLOAD pense-bête — restant à faire

## TODO prioritaire
- Vérifier en réel (création + modification eBay) que les `"` ne ressortent plus en `&quot;` dans les titres/variantes/descriptions.
- Si un cas `&quot;` persiste, tracer la source exacte (DB input, template, payload Inventory API, ou réponse eBay) et corriger à la source.

## TODO secondaire
- Ajouter une interface admin pour gérer `oc_card_grading_company` (actif/inactif + ordre).
- Ajouter un test de non-régression pour la classification:
  - `condition=Graded` + pas de grader dans le titre => raw,
  - grader présent dans le titre => graded,
  - `condition=Ungraded` => raw.

## Notes
- La table `oc_card_grading_company` existe en base et est seedée.
- La logique frontend/backend de blocage save en cas de brand mismatch est déjà en place.
