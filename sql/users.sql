-- name: getUsers
select u.username,u.email,u.first_name,u.last_name,u.middle_name,u.phone, string_agg(r.name,', ') as roles 
			from users u 
			join user_roles ur on ur.user_id = u.id 
			join roles r on r.id = ur.role_id
group by u.username,u.email,u.first_name,u.last_name,u.middle_name,u.phone
order by u.last_name

-- name: createUser
insert into users(username, password, email, last_name, first_name, middle_name, phone) values(?, ?, ?, ?, ?, ?, ?) RETURNING id;

-- name: addRoleToUser
insert into user_roles(user_id,role_id) values(?,?)

-- name: getUserByUsername
select * from users where username = ?;

--name: getUserRoles
select * from user_roles ur join roles r on r.id = ur.role_id where user_id = ?;

-- name: getUser
select u.* from users u where u.id = ?

-- name: getDrivers
select u.* from users u join user_roles ur on ur.user_id = u.id join roles r on r.id = ur.role_id where r.name = 'driver' order by last_name,first_name

--name: loginUser
select * from users where email = ? and password = ?
-- name: getUserAsPerson
select * from users where first_name = ? and last_name = ? and birth = ?;

--name: isUserInRole
select 1 from user_roles ur join roles r on r.id = ur.role_id  where user_id = ? and r.name = ?

--name: getRoles
select * from roles order by name

--name: updateContacts
update users set email = ?, phone = ? where id = ?