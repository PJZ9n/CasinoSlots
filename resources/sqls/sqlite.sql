-- #! sqlite

-- #{ CasinoSlots

    -- #{ savedata

        -- #{ init
CREATE TABLE IF NOT EXISTS savedata (
  name TEXT NOT NULL,
  id INTEGER NOT NULL,
  data JSON NOT NULL
);
        -- #}

        -- #{ add
        -- #:name string
        -- #:id int
        -- #:data string
INSERT INTO savedata (
  name,
  id,
  data
) VALUES (
  :name,
  :id,
  :data
);
        -- #}

        -- #{ update
        -- #:data string
        -- #:name string
        -- #:id int
UPDATE savedata
SET data = :data
WHERE name = :name AND id = :id;
        -- #}

        -- #{ get
        -- #:name string
        -- #:id int
SELECT data FROM savedata
WHERE name = :name AND id = :id;
        -- #}

    -- #}

-- #}