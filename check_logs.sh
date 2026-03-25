#!/bin/bash
# Script pour vérifier les logs après upload

echo "=== Logs saveVariation (merge status) ==="
grep -i "DEBUG saveVariation" /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log | tail -n 50

echo ""
echo "=== Résumé: Cartes merge=0 vs merge=1 ==="
echo -n "Merge=0 (high-value ≥\$10, toujours nouvelles): "
grep -i "MERGE=0" /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log | wc -l
echo -n "Merge=1 (low-value <\$10, allow merging): "
grep -i "Merge: 1" /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log | wc -l
echo -n "Cards merged (UPDATE qty): "
grep -i "FOUND existing card_id" /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log | wc -l
echo -n "New cards inserted: "
grep -i "NEW CARD INSERTED" /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log | wc -l

echo ""
echo "=== Logs d'images ==="
grep -i "DEBUG.*all_images\|DEBUG.*Inserting image\|DEBUG.*NO IMAGES" /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log | tail -n 30

echo ""
echo "=== Vérification DB (dernières 10 cartes) ===" 
mysql -u n7f9655_n7f9655 -p'jnthngrvs01$$' n7f9655_phoenixliquidation -e "SELECT c.card_id, c.title, c.price, c.quantity, c.merge, COUNT(ci.image_id) as imgs FROM oc_card c LEFT JOIN oc_card_image ci ON c.card_id = ci.card_id GROUP BY c.card_id ORDER BY c.date_added DESC LIMIT 10"
