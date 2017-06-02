-- name: getPoints
select * from points order by name;

-- name: getPoint
select * from points where id = ?

--name: getPointByName
select * from points where name = ?

-- name: pointInRoute
select * from route_points where point_id = ? and route_id = ?

-- name: createPoint
insert into points(name,coordinate) values(?,?) RETURNING id;

-- name: removePoint
delete from points where id = ?

-- name: getRoutes
select * from routes;

-- name: getValidateRoutes
select r.id as id, r.name as name from routes r 
		join route_points rp on rp.route_id = r.id
		group by r.id, r.name 
		having count(rp.point_id)>1


--name: getRoutesFullInfo
select *,p.name as pname,r.name as rname from routes r 
				join route_points rp on rp.route_id = r.id
				join points p on p.id = rp.point_id

-- name: createRoute
insert into routes(name) values(?) RETURNING id;

-- name: addPointToRoute
insert into route_points(route_id,point_id,expected_time) values(?,?,?);

-- name: getRoute
select * from routes where id = ?

-- name: getPointsOfRoute
select rp.id as rp_id, rp.expected_time, rp.point_id, p.id, p.name, p.coordinate from route_points rp
			  join points p on rp.point_id = p.id
			  where rp.route_id = ?
			  ORDER BY rp.expected_time 

-- name: pointsOutOfRoute
select p.* from points p where id not in 
(select point_id from route_points rp where rp.route_id = ?) 

--Возьмем все точки маршрута, кроме той, у которой максимальное ожидаемое время
--name: getPointsOfRouteWithoutLast
select id,name from points p left join 
(select rp1.point_id from route_points rp1
left join route_points rp2 on rp1.route_id = rp2.route_id and
rp1.expected_time<rp2.expected_time
where rp1.route_id = ? and rp2.route_id is null) as tmp on
tmp.point_id = p.id where point_id is null

--name: getLastPointInRoute
select rp1.id,rp1.point_id,rp1.expected_time from route_points rp1
left join route_points rp2 on rp1.route_id = rp2.route_id and
rp1.expected_time<rp2.expected_time
where rp1.route_id = ? and rp2.route_id is null

-- name: removePointsOfRoute
delete from route_points where route_id = ?

-- name: removePointFromRoute
delete from route_points rp where rp.point_id = ? and rp.route_id = ?

-- name: removePointFromRoute
delete from route_points rp where rp.point_id = ? and rp.route_id = ?

-- name: removeRoute 
delete from routes where id = ?

-- name: getRequests
select * from requests where route_id = ? 

-- name: getRouteByName
select * from routes r where r.name = ?

--name: getRoutesByPoint
select r.id, r.name from routes r join route_points rp on rp.route_id = r.id where rp.point_id = ?

--name: updateRouteName
update routes  set name = ? where id = ?

--name: updatePointName
update points  set name = ? where id = ?

--name: getRoutePoint
select rp.*,extract(DAY from expected_time) as days,cast(expected_time as time) as time  from route_points rp where id = ?

--name: getNextPoint
select * from route_points rp where route_id = ? and expected_time>? order by expected_time limit 1

--name: getPrevPoint
select * from route_points rp where route_id = ? and expected_time<? order by expected_time DESC limit 1

--name: compareIntervals
select extract(EPOCH from cast(? as interval) - cast(? as interval)) as diff

--name: updateTime
update  route_points set expected_time = ?where id = ?
