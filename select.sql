SELECT COUNT(r.id) as all_count_requests
from request as r
UNION
SELECT COUNT(rh.request_id) as filer_count_requests
from response_headers as rh
         INNER JOIN request r2
                    on r2.id = rh.request_id
WHERE rh.header_key = 'Connection'
  AND rh.header_value = 'keep-alive'
