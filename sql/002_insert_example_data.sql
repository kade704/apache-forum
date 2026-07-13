INSERT INTO users (id, password_hash, email, description) -- 비밀번호: "#admin1234"
    VALUES ('admin', '$2a$12$Mrss/VmLwo3btfDLgQJulONqgMn07Nhnxd/.LP7SR7CvZRCX70EwK', 'admin@example.com', '안녕하세요! 저는 관리자입니다.');
INSERT INTO users (id, password_hash, email, description) -- 비밀번호: "#user1234"
    VALUES ('user', '$2a$12$P0DJqyXcqXyGWxNdlvj0WuDjsyGc85LK6WwQlT0VbsJHulR6Y8I.q', 'user@example.com', '안녕하세요! 저는 일반 사용자입니다.');