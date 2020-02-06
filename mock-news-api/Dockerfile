FROM node:13-slim

WORKDIR /app

COPY package.json package.json
COPY yarn.lock yarn.lock

RUN yarn install

COPY . .

CMD ["yarn", "start"]
