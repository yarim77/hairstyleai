# FCM 알림 설정 체크 결과

## 체크 일시
2025-12-17

---

## 1. Firebase Admin SDK 키 파일

### ✅ 정상 (PASS)

| 항목 | 상태 | 내용 |
|------|------|------|
| 파일 존재 | ✅ | `hairwang-web-app-firebase-adminsdk-fbsvc-b7135e1f94.json` |
| 프로젝트 ID | ✅ | `hairwang-web-app` |
| 클라이언트 이메일 | ✅ | `firebase-adminsdk-fbsvc@hairwang-web-app.iam.gserviceaccount.com` |
| Private Key | ✅ | 정상 포함 |
| 구 키 파일 | ⚠️  | `mars-38372-firebase-adminsdk-60l5a-23f211e854.json` (사용 안 함) |

---

## 2. 푸시 라이브러리 (push.lib.php)

### ✅ 정상 (PASS)

#### Firebase 프로젝트 설정
| 항목 | 설정값 | 상태 |
|------|--------|------|
| 프로젝트 이름 | hairwang-web-app | ✅ |
| 프로젝트 ID | hairwang-web-app | ✅ |
| 프로젝트 번호 | 489393091904 | ✅ |
| 번들 ID | com.hairwang | ✅ |

#### FCM 필드 정의
| 필드명 | 타입 | 필수 | 기본값 | 상태 |
|--------|------|------|--------|------|
| title | String | O | - | ✅ |
| message | String | O | - | ✅ |
| url | String | O | G5_URL | ✅ |
| file_url | String | X | - | ✅ |
| arg1 | String | X | '$arg1' | ✅ |
| arg2 | String | X | '1' | ✅ |
| arg3 | String | X | **'HAIRWANG'** | ✅ |

#### 코드 위치
- **라인 88**: `$arg3 = 'HAIRWANG';` ✅ (정상 설정됨)
- **라인 105**: FCM URL에 `$app['ap_pid']` 사용 ✅
- **라인 108**: 키 파일 경로 `G5_DATA_PATH . '/push/' . $app['ap_key']` ✅

#### Android 푸시 구성 (라인 122-137)
```php
'data' => [
    'title' => $title,              // ✅
    'message' => $memo,             // ✅
    'url' => $url2,                 // ✅
    'arg1' => $arg1,                // ✅
    'arg2' => $arg2,                // ✅
    'arg3' => $arg3,                // ✅ HAIRWANG
]
```
**상태**: ✅ 정상

#### iOS 푸시 구성 (라인 145-170)
```php
'notification' => [
    'title' => $title,              // ✅
    'body' => $memo,                // ✅
],
'data' => [
    'url' => $url2,                 // ✅
    'arg1' => $arg1,                // ✅
    'arg2' => $arg2,                // ✅
    'arg3' => $arg3,                // ✅ HAIRWANG
],
'apns' => [
    'payload' => [
        'aps' => ['sound' => 'default']  // ✅
    ]
]
```
**상태**: ✅ 정상

---

## 3. 푸시 API (push_api.php)

### ✅ 정상 (PASS)

#### 헤더 정보
| 항목 | 내용 | 상태 |
|------|------|------|
| 프로젝트 이름 | hairwang-web-app | ✅ |
| 프로젝트 ID | hairwang-web-app | ✅ |
| 번들 ID | com.hairwang | ✅ |

#### 기본값 설정 (라인 60-62)
```php
$arg1 = isset($_REQUEST['arg1']) ? $_REQUEST['arg1'] : '$arg1';        // ✅
$arg2 = isset($_REQUEST['arg2']) ? $_REQUEST['arg2'] : '1';            // ✅
$arg3 = isset($_REQUEST['arg3']) ? $_REQUEST['arg3'] : 'HAIRWANG';     // ✅
```
**상태**: ✅ 정상 (arg3 기본값이 HAIRWANG으로 설정됨)

---

## 4. 푸시 발송 시스템 (send_push.php)

### ✅ 정상 (PASS)

#### 헤더 정보
| 항목 | 내용 | 상태 |
|------|------|------|
| 프로젝트 이름 | hairwang-web-app | ✅ |
| 수정 이력 | 2025-12-09 변경 기록 | ✅ |

#### ⚠️ 주의사항
**라인 54**: 기본 URL이 `https://edumars.net`으로 설정되어 있음
```php
$url = isset($_POST['url']) ? $_POST['url'] : 'https://edumars.net';
```

**권장 수정**:
```php
$url = isset($_POST['url']) ? $_POST['url'] : 'https://hairwang.com';
```

---

## 5. 종합 점검 결과

### ✅ 정상 작동 확인

| 구분 | 파일 | 상태 | 비고 |
|------|------|------|------|
| 키 파일 | hairwang-web-app-firebase-adminsdk-fbsvc-b7135e1f94.json | ✅ | 정상 |
| 라이브러리 | push.lib.php | ✅ | arg3 = HAIRWANG 정상 |
| API | push_api.php | ✅ | arg3 기본값 HAIRWANG 정상 |
| 발송 시스템 | send_push.php | ⚠️  | 기본 URL 수정 권장 |

---

## 6. 발견된 문제점

### ⚠️ 1. send_push.php 기본 URL

**현재 설정**:
```php
$url = isset($_POST['url']) ? $_POST['url'] : 'https://edumars.net';
```

**문제**: edumars.net으로 설정되어 있음 (구 프로젝트)

**권장 수정**:
```php
$url = isset($_POST['url']) ? $_POST['url'] : 'https://hairwang.com';
```

**파일 위치**: `new_http_v1_hairwang/send_push.php` 라인 54

---

## 7. 서버 업로드 체크리스트

### 필수 업로드 파일

| 파일 | 로컬 경로 | 서버 경로 | 상태 |
|------|-----------|-----------|------|
| 키 파일 | `new_http_v1_hairwang/hairwang-web-app-firebase-adminsdk-fbsvc-b7135e1f94.json` | `/new_http_v1_hairwang/` | 필수 |
| 라이브러리 | `new_http_v1_hairwang/push.lib.php` | `/new_http_v1_hairwang/` | 필수 |
| API | `new_http_v1_hairwang/push_api.php` | `/new_http_v1_hairwang/` | 필수 |
| 발송 시스템 | `new_http_v1_hairwang/send_push.php` | `/new_http_v1_hairwang/` | 권장 수정 후 업로드 |

### 서버 설정 (rb_app 테이블)

```sql
UPDATE rb_app SET
  ap_pid = 'hairwang-web-app',
  ap_key = 'hairwang-web-app-firebase-adminsdk-fbsvc-b7135e1f94.json'
WHERE 1=1;
```

### data/push/ 폴더 확인

서버의 `data/push/` 폴더에 키 파일이 있는지 확인:
```bash
ls -la /var/www/html/data/push/hairwang-web-app-firebase-adminsdk-fbsvc-b7135e1f94.json
```

---

## 8. 테스트 방법

### 1. 개별 푸시 테스트

```bash
curl -X POST "https://hairwang.com/new_http_v1_hairwang/push_api.php" \
  -d "push_type=individual" \
  -d "app=android" \
  -d "user_token=YOUR_FCM_TOKEN" \
  -d "title=테스트 알림" \
  -d "message=푸시 테스트입니다" \
  -d "url=https://hairwang.com" \
  -d "arg3=HAIRWANG"
```

### 2. 전체 푸시 테스트

```bash
curl -X POST "https://hairwang.com/new_http_v1_hairwang/push_api.php" \
  -d "push_type=group" \
  -d "title=전체 공지" \
  -d "message=전체 푸시 테스트입니다" \
  -d "url=https://hairwang.com" \
  -d "arg3=HAIRWANG"
```

---

## 9. 최종 결론

### ✅ 알림 설정 상태: **정상**

- Firebase 프로젝트 설정: ✅ **hairwang-web-app 정상**
- 키 파일: ✅ **정상 존재**
- arg3 필드: ✅ **HAIRWANG 정상 설정**
- Android 푸시: ✅ **정상**
- iOS 푸시: ✅ **정상**

### ⚠️ 권장 수정사항

1. **send_push.php 라인 54**: 기본 URL을 `https://hairwang.com`으로 변경
2. **구 키 파일 삭제**: `mars-38372-firebase-adminsdk-60l5a-23f211e854.json` 파일 삭제 권장 (사용 안 함)

### 📌 다음 단계

1. send_push.php의 기본 URL 수정
2. 서버에 파일 업로드
3. rb_app 테이블 설정 확인
4. 테스트 푸시 발송
5. Android/iOS 앱에서 수신 확인

---

## 문의

문제 발생 시:
- 이메일: support@hairwang.com
- 웹사이트: https://hairwang.com
