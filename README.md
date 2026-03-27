# DOST PES Portal

Laravel migration of the PES portal with:

- Public portal in Blade + Bootstrap
- Admin dashboard for issuances, materials, divisions, DX items, and categories
- Contact message and subscription storage
- Optional Gemini-backed assistant

## MySQL setup

1. Create a MySQL database, for example `dost_pes_portal`.
2. Update the database values in [.env](/c:/Users/admin/Downloads/dost-pes-portal/.env):
   `DB_CONNECTION=mysql`
   `DB_HOST=127.0.0.1`
   `DB_PORT=3306`
   `DB_DATABASE=your_schema_name`
   `DB_USERNAME=your_mysql_user`
   `DB_PASSWORD=your_mysql_password`
3. Run:

```bash
php artisan migrate --seed
php artisan serve
```

## Admin login

Default admin password in `.env`:

```env
ADMIN_PASSWORD=admin123
```

You can replace it with your own password, or use `ADMIN_PASSWORD_HASH` if you want to store a bcrypt hash instead.

## Assistant

Set `GEMINI_API_KEY` in `.env` if you want the assistant to call Gemini. Without it, the assistant uses Laravel-side fallback responses.
