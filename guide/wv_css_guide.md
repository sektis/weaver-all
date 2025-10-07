# 🎨 wv_css 플러그인 가이드

> **Tailwind CSS 기반 자동 반응형 CSS 시스템**

---

## 📋 목차

1. [개요](#개요)
2. [핵심 개념](#핵심-개념)
3. [설치 및 환경 설정](#설치-및-환경-설정)
4. [사용 방법](#사용-방법)
5. [CSS 변수 시스템](#css-변수-시스템)
6. [반응형 시스템](#반응형-시스템)
7. [설정 파일 (config.json)](#설정-파일-configjson)
8. [커스텀 유틸리티](#커스텀-유틸리티)
9. [Blocklist 시스템](#blocklist-시스템)
10. [실전 활용 예시](#실전-활용-예시)
11. [문제 해결](#문제-해결)

---

## 📌 개요

**wv_css 플러그인**은 Tailwind CSS를 기반으로, 자동으로 반응형 CSS 변수를 생성하는 강력한 시스템입니다.

### 핵심 특징

✅ **Tailwind CSS v4 기반**: 최신 Tailwind CSS 활용  
✅ **자동 CSS 변수 생성**: `15px` → `--wv-15` 자동 변환  
✅ **실시간 감시**: `npm run watch`로 파일 변경 자동 감지  
✅ **완전 반응형**: clamp() 함수로 유동적 크기 조정  
✅ **디바이스별 변수**: `--wv-md-48`, `--wv-lg-30` 등 반응형 변수  
✅ **PostCSS 통합**: Tailwind 출력 자동 변환  
✅ **Blocklist 시스템**: 불필요한 클래스 제거로 최적화

---

## 🧩 핵심 개념

### 1. CSS 변수 자동 생성

코드에서 사용한 px 값을 자동으로 CSS 변수로 변환합니다.

```html
<!-- HTML/PHP에서 -->
<div class="mt-[15px] w-[320px]">내용</div>
```

↓ **자동 생성**

```css
/* wv_tailwind.css */
.mt-\[15px\] {
  margin-top: var(--wv-15) !important;
}
.w-\[320px\] {
  width: var(--wv-320) !important;
}
```

↓ **반응형 CSS 변수**

```css
/* wv_responsive.css */
:root {
  --wv-15: calc(var(--wv-standard-value) * 15 / 10);
  --wv-320: calc(var(--wv-standard-value) * 320 / 10);
}
```

### 2. 반응형 시스템

화면 크기에 따라 CSS 변수 값이 자동으로 조정됩니다.

```html
<!-- 반응형 여백 -->
<div class="mt-[48px] md:mt-[24px]">
  PC: 48px, Mobile: 24px (자동 스케일링)
</div>
```

↓ **생성된 CSS**

```css
/* PC */
:root {
  --wv-48: calc(var(--wv-standard-value) * 48 / 10);
}

/* Mobile (991.98px 이하) */
@media (max-width: 991.98px) {
  :root {
    --wv-md-24: calc(var(--wv-standard-value-md) * 24 / 10);
  }
}
```

### 3. clamp() 자동 스케일링

CSS 변수는 clamp() 함수로 최소/최대 값을 유지하며 반응합니다.

```css
--wv-standard-value: clamp(
  calc(10px * var(--wv-ini-min)),     /* 최소값 */
  calc(var(--wv-ini-vw) * 1vw),      /* 가변값 (vw 기반) */
  calc(10px * var(--wv-ini-max))     /* 최대값 */
);
```

**예시**:
- 화면 폭 400px: 작은 값 유지 (min)
- 화면 폭 800px: vw에 비례해서 증가
- 화면 폭 1920px: 큰 값 유지 (max)

---

## 🛠️ 설치 및 환경 설정

### 1. Node.js 및 npm 설치

**Windows 환경**:

1. Node.js 설치: https://nodejs.org/
2. 설치 확인:
```bash
node -v
npm -v
```

### 2. 전역 패키지 설치

```bash
npm install -g tailwindcss @tailwindcss/cli chokidar @sub0709/json-config postcss @tailwindcss/postcss css-generator clean-css
```

**설치 경로 확인**:
```bash
npm root -g
```

**예상 경로**:
- Windows: `C:\Users\{사용자명}\AppData\Roaming\npm\node_modules`
- Mac/Linux: `/usr/local/lib/node_modules`

### 3. 환경 변수 설정 (Windows)

1. **시스템 환경 변수 편집** 열기
2. **환경 변수** 클릭
3. **NODE_PATH** 새로 만들기:
    - 이름: `NODE_PATH`
    - 값: `C:\Users\{사용자명}\AppData\Roaming\npm\node_modules`

### 4. Tailwind Base Layer 수정

**파일**: `{npm_global_path}/tailwindcss/index.css`

```css
/* @layer base {} 안의 모든 속성 삭제 */
@layer base {
  /* 여기를 비워두기 */
}
```

**이유**: 그누보드5 기본 스타일과 충돌 방지

### 5. 디렉토리 구조 확인

```
plugins/wv_css/
├── WvCss.php                  # 메인 PHP 클래스
├── wv_tailwind.css            # 생성된 Tailwind CSS
├── wv_responsive.css          # 생성된 반응형 CSS 변수
├── plugin.php
└── tailwindcss/
    ├── watch_dev.js           # 파일 감시 스크립트
    ├── postcss.config.js      # PostCSS 설정
    ├── tailwind.config.js     # Tailwind 설정
    ├── input.css              # Tailwind 입력 파일
    ├── config.json            # ⭐ 핵심 설정 파일
    ├── blocklist.json         # 자동 생성 (클래스 제외 목록)
    ├── package.json
    └── readme.md
```

---

## 🚀 사용 방법

### 1. Watch 모드 시작

**디렉토리**: `plugins/wv_css/tailwindcss/`

```bash
cd plugins/wv_css/tailwindcss
npm run watch
```

**실행 결과**:
```
complete 0.5sec
```

### 2. 파일 작성

**예시**: `theme/basic/pc/main.php`

```php
<div class="container">
    <h1 class="fs-[32/48/-1.28/700] mt-[24px]">제목</h1>
    <p class="fs-[16/24/0/400/#666] mt-[12px]">내용</p>
    
    <div class="mt-[48px] md:mt-[24px] w-[320px]">
        카드 컨텐츠
    </div>
</div>
```

### 3. 자동 생성 확인

**watch_dev.js가 자동으로**:

1. 파일 변경 감지
2. Tailwind CSS 컴파일 (`wv_tailwind.css` 생성)
3. CSS 변수 스캔
4. 반응형 CSS 생성 (`wv_responsive.css` 생성)

**생성된 파일**:

```css
/* plugins/wv_css/wv_tailwind.css */
.mt-\[24px\] {
  margin-top: var(--wv-24) !important;
}
.mt-\[48px\] {
  margin-top: var(--wv-48) !important;
}
.w-\[320px\] {
  width: var(--wv-320) !important;
}
```

```css
/* plugins/wv_css/wv_responsive.css */
:root {
  --wv-standard-value: clamp(
    calc(10px * 0.8),
    calc(10 / (1440 / 100) * 1vw),
    calc(10px * 1)
  );
  --wv-24: calc(var(--wv-standard-value) * 24 / 10);
  --wv-48: calc(var(--wv-standard-value) * 48 / 10);
  --wv-320: calc(var(--wv-standard-value) * 320 / 10);
}

@media (max-width: 991.98px) {
  :root {
    --wv-standard-value-md: clamp(...);
    --wv-md-24: calc(var(--wv-standard-value-md) * 24 / 10);
  }
}
```

### 4. 페이지에서 확인

브라우저에서 페이지를 열면 자동으로 반응형 CSS가 적용됩니다!

---

## 💡 CSS 변수 시스템

### 변수 네이밍 규칙

| 클래스 | CSS 변수 | 설명 |
|--------|----------|------|
| `mt-[15px]` | `--wv-15` | 기본 (PC) |
| `mt-[27.5px]` | `--wv-27_5` | 소수점 (`.` → `_`) |
| `md:mt-[24px]` | `--wv-md-24` | Mobile (991.98px 이하) |
| `lg:w-[320px]` | `--wv-lg-320` | Large (커스텀) |

### 음수 값 처리

```html
<div class="mt-[-10px]">음수 여백</div>
```

↓ **생성된 CSS**

```css
.mt-\[-10px\] {
  margin-top: calc(var(--wv-10) * -1) !important;
}
```

### 소수점 처리

```html
<div class="w-[27.5px]">27.5px 너비</div>
```

↓ **생성된 CSS**

```css
.w-\[27\.5px\] {
  width: var(--wv-27_5) !important;
}
```

```css
:root {
  --wv-27_5: calc(var(--wv-standard-value) * 27.5 / 10);
}
```

---

## 📱 반응형 시스템

### 브레이크포인트 정의

**파일**: `config.json`

```json
{
  "screens": {
    "md": {
      "max": "991.98px"
    },
    "lg": {
      "max": "1439.98px"
    },
    "xl": {
      "max": "1919.98px"
    }
  }
}
```

### 반응형 클래스 사용

```html
<!-- 기본 48px, 모바일 24px -->
<div class="mt-[48px] md:mt-[24px]">
  반응형 여백
</div>

<!-- 기본 32px, Large 28px, Mobile 20px -->
<h1 class="fs-[32/48] lg:fs-[28/42] md:fs-[20/30]">
  반응형 폰트
</h1>
```

### 디바이스별 표시/숨김

**파일**: `config.json`

```json
{
  "make": {
    "mobile_screen": "md"
  }
}
```

↓ **자동 생성**

```css
@media (max-width: 991.98px) {
  .view-pc {
    display: none !important;
  }
}

@media (min-width: 992px) {
  .view-mobile {
    display: none !important;
  }
}
```

**사용**:

```html
<div class="view-pc">PC에서만 보임</div>
<div class="view-mobile">Mobile에서만 보임</div>
```

---

## ⚙️ 설정 파일 (config.json)

### 전체 구조

```json
{
  "work_root": [
    "../"
  ],
  "screens": {
    "md": {
      "max": "991.98px"
    }
  },
  "make": {
    "selector": ".wv-wrap",
    "mobile_screen": "md",
    "screens": {
      "basic": {
        "width": 1440,
        "min": 0.8,
        "max": 1,
        "add": 1,
        "zoom": {
          "md": {
            "min": 1,
            "max": 1.2,
            "add": 1
          }
        }
      },
      "md": {
        "width": 375,
        "min": 1,
        "max": 1.2,
        "add": 1
      }
    }
  },
  "blocklist_css_list": [],
  "blocklist_css_add": [],
  "blocklist_css_ignore": []
}
```

### 주요 설정 항목

#### 1. work_root

감시할 디렉토리 경로 (상대 경로)

```json
{
  "work_root": [
    "../",           // plugins/wv_css/.. (전체 plugins 디렉토리)
    "../../theme/"   // theme 디렉토리
  ]
}
```

#### 2. screens

Tailwind CSS 브레이크포인트 정의

```json
{
  "screens": {
    "md": {
      "max": "991.98px"
    },
    "lg": {
      "max": "1439.98px"
    }
  }
}
```

#### 3. make.selector

CSS 변수가 적용될 선택자

```json
{
  "make": {
    "selector": ".wv-wrap"
  }
}
```

**결과**:

```css
.wv-wrap {
  --wv-standard-value: clamp(...);
  --wv-15: calc(...);
}
```

#### 4. make.screens (반응형 설정)

각 브레이크포인트별 스케일링 설정

```json
{
  "make": {
    "screens": {
      "basic": {
        "width": 1440,    // 기준 화면 너비
        "min": 0.8,       // 최소 스케일 (80%)
        "max": 1,         // 최대 스케일 (100%)
        "add": 1          // 추가 배율 (100%)
      },
      "md": {
        "width": 375,     // 모바일 기준 너비
        "min": 1,
        "max": 1.2,
        "add": 1
      }
    }
  }
}
```

**생성되는 CSS**:

```css
/* PC (basic) */
:root {
  --wv-ini-width: 1440;
  --wv-ini-min: 0.8;
  --wv-ini-max: 1;
  --wv-ini-add: 1;
  --wv-ini-vw: calc(10 / (1440 / 100));
  --wv-standard-value: clamp(
    calc(10px * 0.8 * 1),
    calc(var(--wv-ini-vw) * 1vw * 1),
    calc(10px * 1 * 1)
  );
}

/* Mobile (md) */
@media (max-width: 991.98px) {
  :root {
    --wv-ini-width-md: 375;
    --wv-ini-min-md: 1;
    --wv-ini-max-md: 1.2;
    --wv-ini-add-md: 1;
    --wv-ini-vw-md: calc(10 / (375 / 100));
    --wv-standard-value-md: clamp(
      calc(10px * 1 * 1),
      calc(var(--wv-ini-vw-md) * 1vw * 1),
      calc(10px * 1.2 * 1)
    );
  }
}
```

#### 5. zoom (특정 포인트 조정)

특정 브레이크포인트에서만 스케일 변경

```json
{
  "basic": {
    "width": 1440,
    "min": 0.8,
    "max": 1,
    "zoom": {
      "md": {
        "min": 1,
        "max": 1.2
      },
      "lg": {
        "min": 0.9,
        "max": 1.1
      }
    }
  }
}
```

**의미**:
- 기본: 80% ~ 100% 스케일
- md 이하: 100% ~ 120% 스케일
- lg 이하: 90% ~ 110% 스케일

---

## 🎨 커스텀 유틸리티

### 1. fs-[] (Font Style)

폰트 크기, 줄 높이, letter-spacing, 굵기, 색상을 한 번에 설정

**형식**: `fs-[size/line/letter/weight/color]`

```html
<h1 class="fs-[32/48/-1.28/700]">
  size: 32px, line-height: 48px, letter-spacing: -0.04em, weight: 700
</h1>

<p class="fs-[16/24/0/400/#666]">
  size: 16px, line-height: 24px, letter-spacing: 0, weight: 400, color: #666
</p>

<span class="fs-[14/-0.56]">
  size: 14px, letter-spacing: -0.04em (line-height, weight는 기본값)
</span>
```

**생성된 CSS**:

```css
.fs-\[32\/48\/-1\.28\/700\] {
  font-size: var(--wv-32) !important;
  line-height: var(--wv-48) !important;
  letter-spacing: -0.04em !important;
  font-weight: 700 !important;
}

.fs-\[16\/24\/0\/400\/\#666\] {
  font-size: var(--wv-16) !important;
  line-height: var(--wv-24) !important;
  letter-spacing: 0 !important;
  font-weight: 400 !important;
  color: #666 !important;
}
```

**letter-spacing 계산**:
```
입력: -1.28
계산: -1.28 / 32 = -0.04em
```

### 2. 반응형 fs-[]

```html
<h1 class="fs-[32/48/-1.28/700] md:fs-[20/30/-0.8/700]">
  PC: 32px, Mobile: 20px
</h1>
```

---

## 🚫 Blocklist 시스템

불필요한 Tailwind 클래스를 제거하여 CSS 파일 크기를 최적화합니다.

### 설정 방법

**파일**: `config.json`

```json
{
  "blocklist_css_list": [
    "../assets/library/bootstrap/vendor/bootstrap/bootstrap.css"
  ],
  "blocklist_css_add": [
    "custom-class-1",
    "custom-class-2"
  ],
  "blocklist_css_ignore": [
    "container",
    "row"
  ]
}
```

### 동작 방식

1. **blocklist_css_list**: CSS 파일에서 클래스명 추출
2. **blocklist_css_add**: 수동으로 추가할 클래스
3. **blocklist_css_ignore**: 제외 목록 (생성 허용)

**결과**: `blocklist.json` 자동 생성

```json
{
  "class_list": [
    "btn",
    "modal",
    "dropdown",
    "navbar"
  ]
}
```

### 효과

```html
<!-- 이 클래스들은 Tailwind에서 생성되지 않음 -->
<button class="btn btn-primary">버튼</button>
<div class="modal">모달</div>

<!-- Bootstrap CSS에서만 사용 -->
```

---

## 🎯 실전 활용 예시

### 1. 반응형 카드 컴포넌트

```php
<div class="w-[360px] md:w-full">
    <div class="p-[24px] md:p-[16px] bg-white rounded-[12px]">
        <h3 class="fs-[24/36/-0.96/700] md:fs-[18/27/-0.72/700]">
            카드 제목
        </h3>
        <p class="fs-[14/21/0/400/#666] md:fs-[13/19.5/0/400/#666] mt-[12px] md:mt-[8px]">
            카드 설명 텍스트입니다.
        </p>
        <button class="fs-[16/24/0/600] mt-[20px] md:mt-[16px] px-[24px] md:px-[16px] py-[12px] md:py-[10px]">
            더보기
        </button>
    </div>
</div>
```

**효과**:
- PC: 360px 고정 너비, 24px 패딩
- Mobile: 100% 너비, 16px 패딩
- 모든 값이 자동 스케일링

### 2. 반응형 타이포그래피

```php
<article>
    <h1 class="fs-[48/64/-1.92/800] md:fs-[32/43/-1.28/800]">
        메인 제목
    </h1>
    
    <h2 class="fs-[36/48/-1.44/700] md:fs-[24/32/-0.96/700] mt-[40px] md:mt-[24px]">
        서브 제목
    </h2>
    
    <p class="fs-[18/30/0/400] md:fs-[16/26/0/400] mt-[16px] md:mt-[12px]">
        본문 텍스트입니다. 읽기 편한 줄 간격과 글자 크기로 설정되었습니다.
    </p>
    
    <p class="fs-[14/22/0/400/#999] md:fs-[12/19/0/400/#999] mt-[8px] md:mt-[6px]">
        작은 글씨 (주석, 날짜 등)
    </p>
</article>
```

### 3. 디바이스별 표시/숨김

```php
<!-- PC 전용 네비게이션 -->
<nav class="view-pc">
    <ul class="flex gap-[32px]">
        <li><a href="#">메뉴1</a></li>
        <li><a href="#">메뉴2</a></li>
        <li><a href="#">메뉴3</a></li>
    </ul>
</nav>

<!-- Mobile 전용 햄버거 메뉴 -->
<button class="view-mobile" onclick="toggleMenu()">
    <i class="fa fa-bars fs-[24/24]"></i>
</button>
```

### 4. 복잡한 레이아웃

```php
<div class="container mx-auto px-[40px] md:px-[20px]">
    <div class="grid grid-cols-3 md:grid-cols-1 gap-[30px] md:gap-[20px]">
        <?php for($i=0; $i<6; $i++): ?>
        <div class="p-[24px] md:p-[16px]">
            <img class="w-full h-[200px] md:h-[150px] object-cover rounded-[8px]">
            <h3 class="fs-[20/28/-0.8/600] md:fs-[16/22/-0.64/600] mt-[16px] md:mt-[12px]">
                항목 제목 <?php echo $i+1; ?>
            </h3>
        </div>
        <?php endfor; ?>
    </div>
</div>
```

### 5. 음수 여백 활용

```php
<div class="relative">
    <img src="banner.jpg" class="w-full h-[400px] md:h-[250px]">
    
    <!-- 이미지에 겹치는 카드 -->
    <div class="absolute bottom-0 left-[40px] md:left-[20px] w-[360px] md:w-[calc(100%-40px)] 
                transform translate-y-[50px] md:translate-y-[30px]
                p-[32px] md:p-[20px] bg-white rounded-[16px]">
        <h2 class="fs-[28/38/-1.12/700] md:fs-[20/28/-0.8/700]">
            겹치는 카드
        </h2>
    </div>
</div>

<!-- 음수 마진으로 위 요소 겹치기 -->
<div class="mt-[-25px] md:mt-[-15px] pt-[75px] md:pt-[45px]">
    다음 섹션
</div>
```

---

## 🐛 문제 해결

### Watch가 실행되지 않을 때

#### 1. Node.js 설치 확인

```bash
node -v
npm -v
```

설치 안 되어 있으면: https://nodejs.org/

#### 2. 전역 패키지 확인

```bash
npm list -g --depth=0
```

필요한 패키지:
- tailwindcss
- @tailwindcss/cli
- chokidar
- @sub0709/json-config
- postcss
- @tailwindcss/postcss
- css-generator
- clean-css

없으면:
```bash
npm install -g {패키지명}
```

#### 3. NODE_PATH 확인

```bash
echo %NODE_PATH%  # Windows
echo $NODE_PATH    # Mac/Linux
```

설정 안 되어 있으면 **환경 변수 설정** 섹션 참고

### CSS 변수가 생성되지 않을 때

#### 1. Watch 모드 실행 확인

```bash
cd plugins/wv_css/tailwindcss
npm run watch
```

**정상**:
```
complete 0.5sec
```

**오류**:
```
Error: Cannot find module...
```

→ 전역 패키지 재설치

#### 2. work_root 경로 확인

**파일**: `config.json`

```json
{
  "work_root": [
    "../"  // plugins/wv_css/.. → 전체 plugins 디렉토리
  ]
}
```

#### 3. 파일 확장자 확인

Watch는 `.php`와 `.css` 파일만 감시합니다.

```json
// watch_dev.js에서
watch_files_arr.push(`${work_root[i]}/**/*.php`);
watch_files_arr.push(`${work_root[i]}/**/*.css`);
```

### 반응형이 작동하지 않을 때

#### 1. wv_responsive.css 로드 확인

브라우저 개발자 도구 → Network → CSS 파일 확인

**있어야 할 파일**:
- `wv_tailwind.css`
- `wv_responsive.css`

#### 2. .wv-wrap 클래스 확인

반응형 CSS 변수는 `.wv-wrap` 선택자 내에 생성됩니다.

```html
<body class="wv-wrap">
  <!-- 여기서 --wv-* 변수 사용 가능 -->
</body>
```

**WvCss.php**가 자동으로 추가합니다.

#### 3. config.json의 screens 확인

```json
{
  "make": {
    "screens": {
      "basic": {
        "width": 1440,
        "min": 0.8,
        "max": 1
      },
      "md": {
        "width": 375,
        "min": 1,
        "max": 1.2
      }
    }
  }
}
```

### fs-[] 유틸리티가 작동하지 않을 때

#### 1. tailwind.config.js 확인

**파일**: `plugins/wv_css/tailwindcss/tailwind.config.js`

```javascript
plugins: [
    plugin(function ({matchUtilities}) {
        matchUtilities({
            fs: (value) => {
                // fs-[] 처리 로직
            }
        })
    })
]
```

#### 2. 문법 확인

```html
<!-- ✅ 올바른 사용 -->
<p class="fs-[16/24/0/400]">텍스트</p>
<p class="fs-[16/24/-0.64/400/#666]">텍스트</p>

<!-- ❌ 잘못된 사용 -->
<p class="fs-[16px/24px/0/400]">텍스트</p>  <!-- px 붙이지 않기 -->
<p class="fs-[16-24-0-400]">텍스트</p>      <!-- - 대신 / 사용 -->
```

### Tailwind 클래스가 생성되지 않을 때

#### 1. Blocklist 확인

**파일**: `blocklist.json`

```json
{
  "class_list": [
    "btn",
    "modal"
  ]
}
```

해당 클래스는 Tailwind에서 생성되지 않습니다.

#### 2. input.css 확인

**파일**: `plugins/wv_css/tailwindcss/input.css`

```css
@import "tailwindcss";
@config "./tailwind.config.js";
```

#### 3. PostCSS 컴파일 확인

```bash
cd plugins/wv_css/tailwindcss
npx postcss ./input.css -o ../wv_tailwind.css
```

---

## 💡 고급 활용 팁

### 1. 커스텀 브레이크포인트 추가

**파일**: `config.json`

```json
{
  "screens": {
    "sm": {
      "max": "575.98px"
    },
    "md": {
      "max": "991.98px"
    },
    "lg": {
      "max": "1439.98px"
    },
    "xl": {
      "max": "1919.98px"
    },
    "xxl": {
      "max": "2559.98px"
    }
  },
  "make": {
    "screens": {
      "sm": {
        "width": 320,
        "min": 1,
        "max": 1.3
      }
    }
  }
}
```

**사용**:

```html
<div class="mt-[64px] xl:mt-[48px] lg:mt-[32px] md:mt-[24px] sm:mt-[16px]">
  5단계 반응형
</div>
```

### 2. 동적 스케일링 조정

특정 섹션만 다른 스케일 적용:

```php
<div class="custom-scale">
    <h1 class="fs-[48/64]">큰 제목</h1>
</div>

<style>
.custom-scale {
    --wv-ini-add: 1.5; /* 150% 스케일 */
}

.custom-scale * {
    /* 자식 요소의 모든 --wv-* 변수가 1.5배 */
}
</style>
```

### 3. CSS 변수 직접 사용

```css
.custom-element {
    width: var(--wv-320);
    height: var(--wv-200);
    margin: var(--wv-20) var(--wv-40);
}

@media (max-width: 991.98px) {
    .custom-element {
        width: var(--wv-md-280);
        height: var(--wv-md-180);
    }
}
```

### 4. JavaScript에서 CSS 변수 활용

```javascript
// CSS 변수 값 가져오기
var rootStyles = getComputedStyle(document.documentElement);
var wv15 = rootStyles.getPropertyValue('--wv-15');

console.log(wv15); // 예: "12px" 또는 "15px" (화면 크기에 따라)

// CSS 변수 값 변경
document.documentElement.style.setProperty('--wv-ini-add', '1.2');
```

### 5. 조건부 스케일링

```php
<?php
$is_mobile = G5_IS_MOBILE;
$scale_add = $is_mobile ? 1.2 : 1;
?>

<style>
.wv-wrap {
    --wv-ini-add: <?php echo $scale_add; ?>;
}
</style>
```

---

## 📊 성능 최적화

### 1. Blocklist 활용

Bootstrap, FontAwesome 등 외부 라이브러리 클래스를 Blocklist에 추가하여 Tailwind CSS 파일 크기 최소화:

```json
{
  "blocklist_css_list": [
    "../assets/library/bootstrap/vendor/bootstrap/bootstrap.css",
    "../assets/library/fontawesome/css/all.css"
  ]
}
```

**효과**: wv_tailwind.css 파일 크기 대폭 감소

### 2. 불필요한 CSS 변수 제거

사용하지 않는 px 값은 코드에서 제거:

```html
<!-- ❌ 나쁜 예: 너무 많은 값 -->
<div class="mt-[1px] mt-[2px] mt-[3px] ... mt-[100px]">

<!-- ✅ 좋은 예: 필요한 값만 -->
<div class="mt-[16px] mt-[24px] mt-[32px]">
```

### 3. Watch 모드 최적화

작업 중이 아닐 때는 Watch 모드 종료:

```bash
# Watch 모드 종료
Ctrl + C
```

**이유**: CPU 사용량 감소

---

## 📋 체크리스트

### 초기 설정 시

- [ ] Node.js 설치 확인
- [ ] npm 전역 패키지 설치
- [ ] NODE_PATH 환경 변수 설정
- [ ] Tailwind Base Layer 비우기
- [ ] config.json 설정
- [ ] work_root 경로 확인
- [ ] screens 브레이크포인트 설정
- [ ] `npm run watch` 실행 확인

### 개발 시

- [ ] Watch 모드 실행 중
- [ ] `complete {시간}sec` 메시지 확인
- [ ] wv_tailwind.css 자동 생성 확인
- [ ] wv_responsive.css 자동 생성 확인
- [ ] 브라우저에서 CSS 변수 적용 확인

### 배포 시

- [ ] Watch 모드 종료
- [ ] wv_tailwind.css 파일 업로드
- [ ] wv_responsive.css 파일 업로드
- [ ] Blocklist 최적화
- [ ] 브라우저 캐시 초기화

---

## 🎓 핵심 개념 요약

1. **자동 변수 생성**: `15px` → `--wv-15` 자동 변환
2. **실시간 감시**: `npm run watch`로 파일 변경 자동 감지
3. **완전 반응형**: clamp() 함수로 화면 크기에 따라 유동적 조정
4. **디바이스별 변수**: `--wv-md-24` 등 브레이크포인트별 변수
5. **PostCSS 통합**: Tailwind 출력 자동 변환 및 최적화
6. **Blocklist**: 불필요한 클래스 제거로 파일 크기 최소화
7. **fs-[] 유틸리티**: 폰트 스타일을 한 번에 설정
8. **유연한 설정**: config.json으로 모든 설정 커스터마이징

---

## 📚 관련 문서

- [Weaver 코어 가이드](weaver_core_guide.md)
- [Assets 플러그인 가이드](assets_guide.md)
- [Layout 플러그인 가이드](layout_guide.md)
- [Tailwind CSS 공식 문서](https://tailwindcss.com/)

---

**문서 버전**: 1.0  
**최종 업데이트**: 2025-10-01  
**작성자**: Claude AI