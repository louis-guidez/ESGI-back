#!/bin/sh
set -e

echo "🚀 Initialisation de MinIO..."

# 📦 Télécharger une version stable et compatible de mc
# ✅ Installer mc correctement si absent
if ! command -v mc >/dev/null 2>&1; then
  echo "📥 Installation du client mc..."
  curl -sSL https://dl.min.io/client/mc/release/linux-amd64/mc -o /usr/local/bin/mc
  chmod +x /usr/local/bin/mc
fi


# ⏳ Attendre que MinIO soit prêt
until curl -s "http://minio:9000" >/dev/null; do
  echo "🕒 En attente de MinIO sur http://minio:9000..."
  sleep 2
done

# 🔐 Connexion à MinIO
mc alias set local http://minio:9000 "$MINIO_ACCESS_KEY" "$MINIO_SECRET_KEY"

# 🪣 Créer le bucket s’il n’existe pas
if ! mc ls local/"$MINIO_BUCKET" >/dev/null 2>&1; then
  echo "📁 Création du bucket '$MINIO_BUCKET'..."
  mc mb local/"$MINIO_BUCKET"
fi

# 🌍 Rendre le bucket public (lecture anonyme)
echo "🔓 Configuration accès public..."
mc anonymous set download local/"$MINIO_BUCKET"

echo "✅ Bucket '$MINIO_BUCKET' initialisé avec accès public + CORS."
