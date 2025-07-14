#!/bin/sh
set -e

echo "🚀 Initialisation MinIO..."

# 🔍 Détection de l'architecture système
ARCH=$(uname -m)

if [ "$ARCH" = "aarch64" ] || [ "$ARCH" = "arm64" ]; then
    MC_ARCH="linux-arm64"
else
    MC_ARCH="linux-amd64"
fi

# 📥 Installation du bon binaire
if ! command -v mc >/dev/null 2>&1; then
    echo "📦 Téléchargement de mc pour $MC_ARCH..."
    curl -sSL "https://dl.min.io/client/mc/release/${MC_ARCH}/mc" -o /usr/local/bin/mc
    chmod +x /usr/local/bin/mc
fi

# ⏳ Attente MinIO
until curl -s "http://minio:9000" >/dev/null; do
    echo "🕒 En attente de MinIO..."
    sleep 2
done

# 🔐 Configuration alias
mc alias set local http://minio:9000 "$MINIO_ROOT_USER" "$MINIO_ROOT_PASSWORD"

# 🪣 Création bucket
if ! mc ls local/"$MINIO_BUCKET" >/dev/null 2>&1; then
    echo "📁 Création du bucket '$MINIO_BUCKET'..."
    mc mb local/"$MINIO_BUCKET"
fi

# 🔓 Rendre public
mc anonymous set download local/"$MINIO_BUCKET"

# 🌐 Configurer CORS
cat <<EOF > /tmp/cors.json
[
  {
    "AllowedOrigin": ["http://localhost:3000"],
    "AllowedMethod": ["GET", "HEAD"],
    "AllowedHeader": ["*"],
    "ExposeHeader": ["ETag"],
    "MaxAgeSeconds": 3000
  }
]
EOF

mc admin bucket cors set local/"$MINIO_BUCKET" /tmp/cors.json

echo "✅ MinIO initialisé (bucket public + CORS)"
