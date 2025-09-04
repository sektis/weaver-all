윈도우에 npm 설치 필수
git bash 등으로 이 디렉토리 내에서 아래 명령어 실행
npm install -g tailwindcss @tailwindcss/cli  chokidar @sub0709/json-config postcss @tailwindcss/postcss css-generator  
에러나면 npm root -g 로 나오는 경로를 아래에 수정
경로상에 괄호등의 특수문자가있으면 jit 모드 안됨 


Windows 환경 변수 설정 열기
시작 → "환경 변수" 검색 → "시스템 환경 변수 편집" → 하단의 환경 변수(N)... 클릭
NODE_PATH 새로 추가:
이름: NODE_PATH
값: C:\Users\sekti\AppData\Roaming\npm\node_modules

node_modules/tailwindcss/index.css에서 base레이어 속성 모두 지우기 ( @layer base {})