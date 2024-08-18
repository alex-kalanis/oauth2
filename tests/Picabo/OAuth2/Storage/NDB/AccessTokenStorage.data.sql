INSERT INTO `oauth_user` (`user_id`, `username`, `password`)
VALUES ('5fcb1af9-d5cd-11', 'test', sha1('password')); -- Use UUID() in production

INSERT INTO `oauth_scope` (`name`, `description`)
VALUES ('profile', 'Allow access to public profile information');
