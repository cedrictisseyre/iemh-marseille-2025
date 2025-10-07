-- Exemple de résultats top 10 pour chaque course GWTS (hommes et femmes)
-- Les id de courses et coureurs doivent correspondre à ceux de ta base
-- Kobe Trail
INSERT INTO results (course_id, runner_id, rank, time) VALUES
(1, 1, 1, '3:45:12'), (1, 2, 2, '3:47:30'), (1, 3, 3, '3:49:10'), (1, 4, 4, '3:50:00'), (1, 5, 5, '3:52:15'), (1, 6, 6, '3:53:40'), (1, 7, 7, '3:54:20'), (1, 8, 8, '3:55:00'), (1, 9, 9, '3:56:10'), (1, 10, 10, '3:57:00');
-- Jin Shan Ling Great Wall Trail
INSERT INTO results (course_id, runner_id, rank, time) VALUES
(2, 1, 1, '4:12:05'), (2, 2, 2, '4:13:20'), (2, 3, 3, '4:15:00'), (2, 4, 4, '4:16:10'), (2, 5, 5, '4:17:30'), (2, 6, 6, '4:18:40'), (2, 7, 7, '4:19:50'), (2, 8, 8, '4:20:30'), (2, 9, 9, '4:21:10'), (2, 10, 10, '4:22:00');
-- Il Golfo dell'Isola Trail
INSERT INTO results (course_id, runner_id, rank, time) VALUES
(3, 1, 1, '2:55:00'), (3, 2, 2, '2:56:10'), (3, 3, 3, '2:57:20'), (3, 4, 4, '2:58:30'), (3, 5, 5, '2:59:40'), (3, 6, 6, '3:00:50'), (3, 7, 7, '3:01:10'), (3, 8, 8, '3:02:20'), (3, 9, 9, '3:03:30'), (3, 10, 10, '3:04:40');
-- Zegama-Aizkorri
INSERT INTO results (course_id, runner_id, rank, time) VALUES
(4, 1, 1, '3:30:00'), (4, 2, 2, '3:31:10'), (4, 3, 3, '3:32:20'), (4, 4, 4, '3:33:30'), (4, 5, 5, '3:34:40'), (4, 6, 6, '3:35:50'), (4, 7, 7, '3:36:10'), (4, 8, 8, '3:37:20'), (4, 9, 9, '3:38:30'), (4, 10, 10, '3:39:40');
-- Broken Arrow Skyrace
INSERT INTO results (course_id, runner_id, rank, time) VALUES
(5, 1, 1, '2:40:00'), (5, 2, 2, '2:41:10'), (5, 3, 3, '2:42:20'), (5, 4, 4, '2:43:30'), (5, 5, 5, '2:44:40'), (5, 6, 6, '2:45:50'), (5, 7, 7, '2:46:10'), (5, 8, 8, '2:47:20'), (5, 9, 9, '2:48:30'), (5, 10, 10, '2:49:40');
-- Tepec Trail
INSERT INTO results (course_id, runner_id, rank, time) VALUES
(6, 1, 1, '3:10:00'), (6, 2, 2, '3:11:10'), (6, 3, 3, '3:12:20'), (6, 4, 4, '3:13:30'), (6, 5, 5, '3:14:40'), (6, 6, 6, '3:15:50'), (6, 7, 7, '3:16:10'), (6, 8, 8, '3:17:20'), (6, 9, 9, '3:18:30'), (6, 10, 10, '3:19:40');
-- Salomon Pitz Alpine Glacier Trail
INSERT INTO results (course_id, runner_id, rank, time) VALUES
(7, 1, 1, '4:00:00'), (7, 2, 2, '4:01:10'), (7, 3, 3, '4:02:20'), (7, 4, 4, '4:03:30'), (7, 5, 5, '4:04:40'), (7, 6, 6, '4:05:50'), (7, 7, 7, '4:06:10'), (7, 8, 8, '4:07:20'), (7, 9, 9, '4:08:30'), (7, 10, 10, '4:09:40');
-- Sierre-Zinal
INSERT INTO results (course_id, runner_id, rank, time) VALUES
(8, 1, 1, '2:35:00'), (8, 2, 2, '2:36:10'), (8, 3, 3, '2:37:20'), (8, 4, 4, '2:38:30'), (8, 5, 5, '2:39:40'), (8, 6, 6, '2:40:50'), (8, 7, 7, '2:41:10'), (8, 8, 8, '2:42:20'), (8, 9, 9, '2:43:30'), (8, 10, 10, '2:44:40');
-- Ledro Sky Trentino Grand Finale
INSERT INTO results (course_id, runner_id, rank, time) VALUES
(9, 1, 1, '3:20:00'), (9, 2, 2, '3:21:10'), (9, 3, 3, '3:22:20'), (9, 4, 4, '3:23:30'), (9, 5, 5, '3:24:40'), (9, 6, 6, '3:25:50'), (9, 7, 7, '3:26:10'), (9, 8, 8, '3:27:20'), (9, 9, 9, '3:28:30'), (9, 10, 10, '3:29:40');
-- Pour les femmes, il suffit d'utiliser les id des coureuses (ex: 2, 4, 6, 8, 10, etc.) et d'adapter les temps.