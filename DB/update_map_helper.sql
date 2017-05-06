# Mice missing from mhmh
INSERT IGNORE INTO mhmaphelper.mice (name, mhhh_id)
SELECT mhhh.name, mhhh.id
FROM mhhunthelper.mice mhhh
LEFT JOIN mhmaphelper.mice mhmh ON UPPER(mhhh.name) LIKE UPPER(mhmh.name)
WHERE mhmh.id IS NULL;

# Cheese missing from mhmh
INSERT IGNORE INTO mhmaphelper.cheeses (name, mhhh_id)
SELECT mhhh.name, mhhh.id
FROM mhhunthelper.cheese mhhh
LEFT JOIN mhmaphelper.cheeses mhmh ON UPPER(mhhh.name) LIKE UPPER(mhmh.name)
WHERE mhmh.id IS NULL;

# Stages missing from mhmh
INSERT IGNORE INTO mhmaphelper.stages (name, mhhh_id)
SELECT mhhh.name, mhhh.id
FROM mhhunthelper.stages mhhh
LEFT JOIN mhmaphelper.stages mhmh ON UPPER(mhhh.name) LIKE UPPER(mhmh.name)
WHERE mhmh.id IS NULL;

# locations missing from mhmh
INSERT IGNORE INTO mhmaphelper.locations (name, mhhh_id)
SELECT mhhh.name, mhhh.id
FROM mhhunthelper.locations mhhh
LEFT JOIN mhmaphelper.locations mhmh ON UPPER(mhhh.name) LIKE UPPER(mhmh.name)
WHERE mhmh.id IS NULL;

# update ids in mhmh
UPDATE mhmaphelper.cheeses c1
INNER JOIN mhhunthelper.cheese c2 ON UPPER(c1.name) LIKE UPPER(c2.name) SET c1.mhhh_id = c2.id;
--
UPDATE mhmaphelper.locations c1
INNER JOIN mhhunthelper.locations c2 ON UPPER(c1.name) LIKE UPPER(c2.name) SET c1.mhhh_id = c2.id;
--
UPDATE mhmaphelper.mice c1
INNER JOIN mhhunthelper.mice c2 ON UPPER(c1.name) LIKE UPPER(c2.name) SET c1.mhhh_id = c2.id;
--
UPDATE mhmaphelper.stages c1
INNER JOIN mhhunthelper.stages c2 ON UPPER(c1.name) LIKE UPPER(c2.name) SET c1.mhhh_id = c2.id;


# add setups stage
INSERT INTO mhmaphelper.setups (location_id, mouse_id, cheese_id, stage_id)
SELECT DISTINCT l.id, m.id, c.id, st.id #, l.name, st.name
FROM mhhunthelper.hunts h
INNER JOIN mhmaphelper.locations l ON h.location_id = l.mhhh_id
INNER JOIN mhmaphelper.mice m ON h.mouse_id = m.mhhh_id
INNER JOIN mhmaphelper.cheeses c ON h.cheese_id = c.mhhh_id
LEFT JOIN mhmaphelper.stages st ON h.stage_id = st.mhhh_id
LEFT JOIN mhmaphelper.setups s ON c.id = s.cheese_id AND m.id = s.mouse_id AND l.id = s.location_id AND s.stage_id <=> st.id
WHERE h.mouse_id IS NOT NULL AND s.id IS NULL AND l.id NOT IN (44, 57); #not in toxic spill or WRift

# remove regular (1) cheese where gouda (76) or brie (26) exist
#SELECT count(*)
DELETE s1
FROM mhmaphelper.setups s1
INNER JOIN mhmaphelper.setups s2 ON s1.mouse_id = s2.mouse_id AND s1.location_id = s2.location_id AND s2.cheese_id IN (76,26)
WHERE s1.cheese_id = 1;

# update ar with stages
UPDATE mhmaphelper.setups s2
INNER JOIN (
SELECT ROUND(COUNT(DISTINCT h2.timestamp) / COUNT(DISTINCT(h.timestamp)) * 10000) AS ar, s.id
FROM mhmaphelper.setups s
INNER JOIN mhmaphelper.locations l ON s.location_id = l.id
INNER JOIN mhmaphelper.cheeses c ON s.cheese_id = c.id
INNER JOIN mhmaphelper.mice m ON s.mouse_id = m.id
INNER JOIN mhmaphelper.stages st ON s.stage_id = st.id
INNER JOIN mhhunthelper.hunts h ON c.mhhh_id = h.cheese_id AND l.mhhh_id = h.location_id AND st.mhhh_id = h.stage_id
LEFT JOIN mhhunthelper.hunts h2 ON c.mhhh_id = h2.cheese_id AND l.mhhh_id = h2.location_id AND st.mhhh_id = h2.stage_id AND m.mhhh_id = h2.mouse_id
GROUP BY s.id
HAVING COUNT(DISTINCT h2.timestamp) > 0 AND COUNT(DISTINCT h.timestamp) > 10
) AS s3 ON s2.id = s3.id SET s2.ar = s3.ar;

# update ar without stages
UPDATE mhmaphelper.setups s2
INNER JOIN (
SELECT ROUND(COUNT(DISTINCT h2.timestamp) / COUNT(DISTINCT(h.timestamp)) * 10000) AS ar, s.id, s.stage_id
FROM mhmaphelper.setups s
INNER JOIN mhmaphelper.locations l ON s.location_id = l.id
INNER JOIN mhmaphelper.cheeses c ON s.cheese_id = c.id
INNER JOIN mhmaphelper.mice m ON s.mouse_id = m.id
INNER JOIN mhhunthelper.hunts h ON c.mhhh_id = h.cheese_id AND l.mhhh_id = h.location_id AND h.stage_id IS NULL
LEFT JOIN mhhunthelper.hunts h2 ON c.mhhh_id = h2.cheese_id AND l.mhhh_id = h2.location_id AND h2.stage_id IS NULL AND m.mhhh_id = h2.mouse_id
GROUP BY s.id
HAVING COUNT(DISTINCT h2.timestamp) > 0 AND COUNT(DISTINCT h.timestamp) > 10 AND s.stage_id IS NULL
) AS s3 ON s2.id = s3.id SET s2.ar = s3.ar;