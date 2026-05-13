FROM node:20-alpine

WORKDIR /app

# Copy package files first for layer caching
COPY package*.json ./
RUN npm install

# Copy the rest of the source
COPY . .

EXPOSE 5173

# --host exposes Vite to all network interfaces inside the container
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
