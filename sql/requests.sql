-- name: createRequestNew
INSERT INTO requests(route_id,start_time,manager_id,driver_id,status) VALUES(?,?,?,?,'request') RETURNING id;

--name: getEndTime
SELECT rp1.expected_time + start_time FROM requests req JOIN route_points rp1 ON rp1.route_id = req.id
LEFT JOIN route_points rp2 ON rp1.route_id = rp2.route_id AND
rp1.expected_time<rp2.expected_time WHERE rp2.route_id is null
AND rp1.route_id = ?

--name: isCanCreate
SELECT 1 as permission FROM requests req JOIN facts f1 ON f1.request_id = req.id
LEFT JOIN facts f2 ON f1.request_id = f2.request_id AND f1.expected_time<f2.expected_time 
WHERE f2.request_id is null 
AND f1.expected_time + start_time + cast('10:00:00' as interval) > ?  
AND driver_id = ?

--name: assignedRoutes
SELECT req.id as id,r.name as name,req.start_time,f1.expected_time + start_time as end_time, status FROM 
		requests req JOIN facts f1 ON f1.request_id = req.id
                JOIN routes r ON r.id = req.route_id
LEFT JOIN facts f2 ON f1.request_id = f2.request_id AND
f1.expected_time<f2.expected_time WHERE f2.request_id is null AND driver_id=? AND 
status in ('request','process')
ORDER BY start_time

--name: completedRoutes
SELECT req.id as id,r.name as name,req.start_time,f1.expected_time + start_time as end_time, status FROM 
		requests req JOIN facts f1 ON f1.request_id = req.id
                JOIN routes r ON r.id = req.route_id
LEFT JOIN facts f2 ON f1.request_id = f2.request_id AND
f1.expected_time<f2.expected_time WHERE f2.request_id is null AND driver_id=? AND 
status in ('complete')


--name: removeFacts
DELETE FROM facts WHERE request_id = ?

--name: removeRequest
DELETE FROM requests WHERE id = ? 



-- name: getActiveRequests
SELECT * FROM requests WHERE status in ('process','complete') AND route_id = ?

--name: removeRequestsByStatus
DELETE FROM requests r WHERE r.route_id = ? AND status = ?

--name: getCompleteRequestsFullInfo
SELECT *, CASE
	WHEN end_time - plan_time>cast('00:00:00' as interval) THEN end_time - plan_time
	ELSE '00:00:00' 
END as late FROM(
SELECT request_id,driver_id,last_name||' '||first_name||' '||middle_name as fio,start_time,max(start_time+fact_time) as end_time,max(start_time+expected_time) as plan_time
FROM requests req JOIN facts f ON f.request_id = req.id JOIN users u ON u.id = req.driver_id
WHERE status = 'complete'
GROUP BY request_id,driver_id,last_name,first_name,middle_name,req.start_time) as aaa
ORDER BY plan_time 

--name: getRequest 
SELECT req.*,
			 drivers.last_name||' '||drivers.first_name||' '||drivers.middle_name as driver_fio,
			 managers.last_name||' '||managers.first_name||' '||managers.middle_name as manager_fio,
			 r.name
			 FROM requests req 
			 JOIN users drivers ON req.driver_id = drivers.id
			 JOIN users managers ON req.manager_id = managers.id
			 JOIN routes r ON r.id = req.route_id
		WHERE req.id = ?

--name: getRequestInfo
SELECT rp.id as rpid, req.id as reqid, p.id,p.name,f.is_good_reason,f.comment,
		req.start_time + rp.expected_time as plan_time,
		req.start_time + f.fact_time as fact_time,
		case 
			when f.fact_time - f.expected_time>cast('00:00:00' as interval) THEN  f.fact_time - f.expected_time 
			ElSE '00:00:00' 
		END as late
		 FROM requests req
			JOIN route_points rp ON rp.route_id = req.route_id
			LEFT JOIN facts f ON f.request_id = req.id AND rp.id= f.rp_id
JOIN points p ON p.id = rp.point_id WHERE req.id = ?

--name: getRP
SELECT * FROM route_points rp WHERE rp.id = ?

--name: savereason
UPDATE facts f SET  is_good_reason = ?, comment = ? WHERE rp_id = ? AND request_id = ?


--name: getNextPointInRoute
SELECT  * FROM facts f 
WHERE expected_time>cast(? as interval)
AND request_id = ?
ORDER BY expected_time  limit 2

--name: createFacts
INSERT INTO facts(rp_id,request_id,expected_time,fact_time,is_good_reason,comment)
VALUES(?,?,?,?,?,?)


--name: updateFactTime
UPDATE facts f SET fact_time = ? WHERE rp_id = ? AND request_id = ?

--name: updateStatus
UPDATE requests SET status = ? WHERE id = ?

--name: getNextRoute
SELECT 
	r.id AS route_id,
	r.name AS rname,
	req.id AS request_id,
	start_time
FROM requests req 
	JOIN routes r ON r.id = req.route_id
WHERE driver_id = ? AND start_time > ? AND status = 'request' 
ORDER BY start_time 
LIMIT 1

	
--name: planRequest
SELECT 
	p.id AS point_id,
	rp_id,
	p.name AS pname,
	req.start_time + f.expected_time as plan_time
FROM requests req 
	 JOIN facts f ON f.request_id = req.id
	 JOIN route_points rp ON rp.id = f.rp_id 
	 JOIN points p ON p.id = rp.point_id
WHERE req.driver_id = ? AND req.id = ? 
ORDER BY f.expected_time

--name: getStartPoint
SELECT * FROM facts
WHERE request_id = ? AND expected_time = '00:00:00'
LIMIT 1
