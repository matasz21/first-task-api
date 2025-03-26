1. Create .env.local and add:
  a) DATABASE_URL
  b) JWT_PASSPHRASE

2. Generate JWT keys:
php bin/console lexik:jwt:generate-keypair

For fixtures:
bin/console doctrine:fixtures:load

For testing:
php bin/phpunit --filter PostServiceTest
