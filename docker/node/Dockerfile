FROM node:22-alpine

WORKDIR /app

# Копируем package.json и package-lock.json (если есть) для установки зависимостей
COPY ./frontend/package*.json ./

# Устанавливаем зависимости
RUN npm install

# Остальное будет смонтировано через volumes
CMD ["npm", "run", "build"]