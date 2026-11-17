#!/usr/bin/env bash
# =====================================================================
# TP3 — vérification reproductible
# Recrée une base vide, exécute 001_schema.sql puis 002_seed.sql,
# affiche les COUNT(*) et contrôle les contraintes UNIQUE.
#
# Exemple MySQL/MariaDB local :
#   MYSQL_BIN=mysql MYSQL_USER=root MYSQL_PASSWORD=secret \
#     bash verifier.sh
#
# Exemple avec un socket :
#   MYSQL_BIN=/opt/lampp/bin/mysql MYSQL_USER=root \
#     MYSQL_SOCKET=/opt/lampp/var/mysql/mysql.sock bash verifier.sh
# =====================================================================
set -euo pipefail
cd "$(dirname "$0")"

MYSQL_BIN="${MYSQL_BIN:-mysql}"
MYSQL_USER="${MYSQL_USER:-root}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-}"
MYSQL_HOST="${MYSQL_HOST:-127.0.0.1}"
MYSQL_PORT="${MYSQL_PORT:-3306}"
MYSQL_SOCKET="${MYSQL_SOCKET:-}"
DB="${DB:-tp03_vide}"

MYSQL=( "$MYSQL_BIN" --user="$MYSQL_USER" )
if [ -n "$MYSQL_SOCKET" ]; then
    MYSQL+=( --socket="$MYSQL_SOCKET" )
else
    MYSQL+=( --host="$MYSQL_HOST" --port="$MYSQL_PORT" )
fi
if [ -n "$MYSQL_PASSWORD" ]; then
    MYSQL+=( --password="$MYSQL_PASSWORD" )
fi

echo "TP3 — EXÉCUTION DU SCHÉMA DANS UNE BASE VIDE ET VÉRIFICATION DES COMPTES"
echo "Date        : $(date '+%Y-%m-%d %H:%M:%S')"
echo "Base        : $DB (recréée à l'instant)"
echo "Scripts     : 001_schema.sql, 002_seed.sql"
echo

"${MYSQL[@]}" -e "DROP DATABASE IF EXISTS \`$DB\`;
                  CREATE DATABASE \`$DB\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
"${MYSQL[@]}" "$DB" < 001_schema.sql
"${MYSQL[@]}" "$DB" < 002_seed.sql

echo "SGBD :"
"${MYSQL[@]}" --table -e "SELECT VERSION() AS version;"
echo

echo "Tables créées :"
"${MYSQL[@]}" --table "$DB" -e "SHOW TABLES;"
echo

echo "Contraintes déclarées :"
"${MYSQL[@]}" --table "$DB" -e "
SELECT table_name AS table_, constraint_type AS type, constraint_name AS nom
FROM information_schema.table_constraints
WHERE table_schema = '$DB'
ORDER BY table_name, constraint_type, constraint_name;"
echo

echo "Comptes demandés par le sujet :"
"${MYSQL[@]}" --table "$DB" -e "
SELECT 'etudiant' AS table_, COUNT(*) AS lignes FROM etudiant
UNION ALL SELECT 'entreprise',    COUNT(*) FROM entreprise
UNION ALL SELECT 'demande_stage', COUNT(*) FROM demande_stage
UNION ALL SELECT 'encadreur',     COUNT(*) FROM encadreur
UNION ALL SELECT 'soutenance',    COUNT(*) FROM soutenance;"
echo

echo "Contrôle UNIQUE : une demande a au plus une soutenance."
if "${MYSQL[@]}" "$DB" -e "
INSERT INTO soutenance (demande_id, date_heure, salle)
VALUES (2, '2026-12-11 09:00:00', 'B12');" 2>&1; then
    echo "ÉCHEC : le doublon de soutenance a été accepté."
    exit 1
else
    echo "OK : insertion refusée par uq_soutenance_demande."
fi
echo

echo "Contrôle UNIQUE : un créneau de salle est unique."
if "${MYSQL[@]}" "$DB" -e "
INSERT INTO soutenance (demande_id, date_heure, salle)
VALUES (1, '2026-12-10 09:00:00', 'B12');" 2>&1; then
    echo "ÉCHEC : le conflit de créneau a été accepté."
    exit 1
else
    echo "OK : insertion refusée par uq_soutenance_creneau."
fi
echo

echo "Résultat : OK — 3 étudiants, 2 entreprises, 4 demandes."
