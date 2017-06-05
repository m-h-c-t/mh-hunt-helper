SELECT timestamp, l.name as location, s.name as stage, t.name as trap, b.name as base, ch.name as charm, h.shield, h.attracted, h.caught, m.name as mouse, c.name as cheese
 FROM hunts h
 LEFT JOIN locations l on h.location_id = l.id
 LEFT JOIN stages s on h.stage_id = s.id
 LEFT JOIN mice m on h.mouse_id = m.id
 LEFT JOIN cheese c on h.cheese_id = c.id
 LEFT JOIN traps t on h.trap_id = t.id
 LEFT JOIN bases b on h.base_id = b.id
 LEFT JOIN charms ch on h.charm_id = ch.id;