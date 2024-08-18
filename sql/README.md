Base DB structure
=================
This is really just a base structure. You can define your own and then create storage using interfaces from `Drahak\OAuth2\Storage` (just like in default NDB implementation).

PostgreSQL
----------
Since Picabo\OAuth2 uses UUID for some primary keys, you will probably need to create postgres extension `uuid-ossp`. At least in OAuth2 database schema.

    CREATE EXTENSION "uuid-ossp";