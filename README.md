# Apache Forum - Apache + PHP 기반 게시판

## 샘플 계정 목록

| 계정 | 비밀번호 |
|---|---|
| `admin` | `#admin1234` |
| `user` | `#user1234` |

## POST 요청 종류

| 경로 | 기능 | 파라미터 |
|---|---|---|
| `html/actions/signup_user.php` | 회원가입 | `csrf_token`, `user_id`, `email`, `password`, `password_repeat` |
| `html/actions/login_user.php` | 로그인 | `csrf_token`, `user_id`, `password` |
| `html/actions/logout_user.php` | 로그아웃 | `csrf_token` |
| `html/actions/create_post.php` | 게시글 작성 | `csrf_token`, `title`, `content`, `image_file` |
| `html/actions/edit_post.php` | 게시글 수정 | `csrf_token`, `post_id`, `title`, `content`, `image_file` |
| `html/actions/delete_post.php` | 게시글 삭제 | `csrf_token`, `post_id` |
| `html/actions/create_comment.php` | 댓글 작성 | `csrf_token`, `post_id`, `content` |
| `html/actions/delete_comment.php` | 댓글 삭제 | `csrf_token`, `post_id`, `comment_id` |
| `html/actions/edit_profile.php` | 프로필(이메일/소개) 수정 | `csrf_token`, `email`, `description` |
| `html/actions/change_password.php` | 비밀번호 변경 | `csrf_token`, `curr_password`, `new_password` |
| `html/actions/delete_user.php` | 회원 탈퇴 | `csrf_token`, `curr_password` |

---

## 적용된 보안 기능

### 공통 보안
- **HTTP 메서드 제한**: `includes/post_check.php`에서 POST 이외 요청 차단(405).
- **SQL Injection 방지**: DB 접근 시 `prepare + bind_param` 기반 Prepared Statement 사용.
- **세션 보안 설정**: `includes/session.php`
  - `session.cookie_httponly=1`
  - `session.use_strict_mode=1`
- **세션 고정 공격 완화**: 로그인 성공 시 `session_regenerate_id(true)` 실행.

### CSRF 방어
- **토큰 발급**: `includes/csrf_token.php`에서 세션 기반 토큰 생성.
- **토큰 검증**: `includes/csrf_check.php`에서 `hash_equals`로 검증.

### 인증/인가
- **인증(로그인 필요) 검증**: `includes/login_check.php`로 로그인 여부 확인.
- **작성자 권한 검증**:
  - 게시글 수정/삭제 시 본인 글만 처리
  - 댓글 삭제 시 본인 댓글만 처리
- **계정 민감 작업 재검증**:
  - 비밀번호 변경/회원 탈퇴 시 현재 비밀번호 확인(`password_verify`)

### 입력값 검증 및 XSS 완화
- **필수값 검증**: 각 POST 액션에서 누락 입력 400 처리.
- **비밀번호 정책 검증**: 길이(8~20), 영문/숫자/특수문자 포함 규칙 적용.
- **이메일 형식 검증**: `filter_var(..., FILTER_VALIDATE_EMAIL)` 사용.
- **출력/저장 전 이스케이프**: 주요 텍스트 입력에 `htmlspecialchars(..., ENT_QUOTES, "UTF-8")` 적용.

### 파일 업로드 보안
- **MIME 타입 검증**: `finfo_file`로 실제 MIME 타입 확인.
- **MIME 타입 화이트리스트**: jpeg/png/gif/webp만 허용.
- **랜덤 파일명 부여**: `random_bytes` 기반 파일 ID 생성.
- **업로드 경로 분리**: 웹 루트 외부 `/data/images/`에 저장.

