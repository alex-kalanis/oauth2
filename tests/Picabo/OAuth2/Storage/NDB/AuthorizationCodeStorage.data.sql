INSERT INTO `oauth_user` (`user_id`, `username`, `password`)
VALUES ('5fcb1ca9-7372-11', 'test', sha1('password'));

INSERT INTO oauth_scope (`name`, `description`)
VALUES ('profile', 'Allow access to public profile information');
