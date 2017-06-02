-- name: whereIsDriversNow
SELECT u.id as uid,u.last_name||' '||u.first_name||' '||u.middle_name as FIO,
		p.id as pid,
		p.name as name,
		p.coordinate,
		f.fact_time + req.start_time as time,
		f.expected_time as expected_time,
		f.request_id,
		req.route_id,
		f.rp_id as rpid,
		req.start_time
FROM     facts f
	 join route_points rp on f.rp_id = rp.id
	 join requests req on req.id = f.request_id
	 join points p on p.id = rp.point_id
	 join users u on u.id = req.driver_id
where f.fact_time is not null  and ? in (0, manager_id) and req.status = 'process'
and ? in (0,u.id)
order by f.expected_time DESC
--name: getDrivers
select u.* from users u join user_roles ur on ur.user_id = u.id join roles r on r.id = ur.role_id where r.name = 'driver' order by last_name,first_name

--name: getActiveRoutesByManager
select req.id,req.route_id,p.name, p.coordinate from requests req 
							join routes r on r.id = req.route_id
							join route_points rp on rp.route_id = req.route_id
							join points p on p.id = rp.point_id
							where req.manager_id = ? and req.status = 'process'
							order by route_id,expected_time 

--name: rating_old
SELECT driver_id,
		u.last_name||' '||u.first_name||' '||u.middle_name as fio, 
		avg( f.fact_time - f.expected_time - coalesce(justified.jtime,cast('00:00:00' as interval)) ) as avg_time 
FROM facts f 
	join requests req on req.id = f.request_id 
	join users u on u.id = req.driver_id
	left join 
	(
		select f1.request_id,
		f1.rp_id,LEAST(MAX(F2.FACT_TIME-F2.EXPECTED_TIME),f1.fact_time-f1.expected_time) as jtime 
		from facts f1 
				left join facts f2 on f1.request_id = f2.request_id and f1.rp_id>=f2.rp_id 
				where F2.IS_GOOD_REASON 
		group by f1.request_id,f1.rp_id,f1.fact_time,f1.expected_time) as justified on f.request_id = justified.request_id and f.rp_id = justified.rp_id 
	where status = 'complete' and f.fact_time - f.expected_time > cast('00:00:00' as interval) 
	group by driver_id,u.last_name,u.first_name,u.middle_name
	order by 3 asc

--name: rating
SELECT  req.driver_id,
        u.last_name||' '||u.first_name||' '||u.middle_name as fio, 
        avg(late) as avg_time
FROM requests req
     join users u on u.id = req.driver_id
     join (select driver_id, GREATEST(fact_time-expected_time - max(
case is_good_reason when true then fact_time - expected_time else '00:00:00' end) OVER (partition by request_id  order by rp_id),cast('00:00:00' as interval)) as late from facts f join requests req on req.id = f.request_id
where fact_time-expected_time>'00:00:00' 
order by request_id,rp_id) as justified on justified.driver_id = req.driver_id
group by req.driver_id,u.last_name,u.first_name,u.middle_name
order by 3 asc

