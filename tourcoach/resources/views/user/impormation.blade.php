@extends('master')
@push('css')
<link rel="stylesheet" href="/css/ModifyMemberInformation.css">
<link rel="stylesheet" href="/css/ModifyMemberInformation.css">
<link rel="stylesheet" href="/css/footer.css">
@endpush
@section('content')
    {{--<span>회원정보 수정</span>--}}

    {{--<form action="/user/modify/userpass" method="post">--}}
        {{--<input type="text" name="userpass">--}}
    {{--</form>--}}

    <div class="containor">
        <section id="Profile" class="profile">
            <div class="profile-title-box">
                <div class="profile-title-main">
                    <img src="/img/RESOURCE/ModifyMemberInformation/ic_회원정보수정.png" alt="" draggable="false">
                </div>
                <div class="profile-title-sub">
                    가입하신 회원정보를 수정하실 수 있습니다.[ tourcoach ]님의 정보입니다.</br>
                    회원정보는 개인정보 처리방침에 따라 안전하게 보호되며, 회원님의 명백한 동의없이 공개 또는 제3자에게 제공되지 않습니다.
                </div>

                <section class="profile-box">
                    <div class="profile-left">
                        <div class="profile-title">
                            <div class="profile-name">
                                사용자 이름
                            </div>
                            <div class="profile-subName">
                                User name
                            </div>
                        </div>
                        <div class="profile-subTitle">
                            실명 정보(이름, 생년월일, 성별, 고유 식별정보)가 번경된 경우 본인 확인을 통해 정보를 수정하실 수 있습니다.
                        </div>
                        <div class="profile-index">
                            홍길동
                        </div>
                    </div>
                    {{--<div class="profile-right">--}}
                        {{--<div class="fix-btn">--}}
                            {{--수정하기--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </section>
                <section class="profile-box">
                    <div class="profile-left">
                        <div class="profile-title">
                            <div class="profile-name">
                                아이디
                            </div>
                            <div class="profile-subName">
                                User ID
                            </div>
                        </div>
                        <div class="profile-subTitle">
                            사용자의 아이디 입니다.
                        </div>
                        <div class="profile-index">
                            tourcoach
                        </div>
                    </div>
                    {{--<div class="profile-right">--}}
                        {{--<div class="fix-btn">--}}
                            {{--수정하기--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </section>
                <section class="profile-box">
                    <div class="profile-left">
                        <div class="profile-title">
                            <div class="profile-name">
                                비밀번호
                            </div>
                            <div class="profile-subName">
                                Password
                            </div>
                        </div>
                        <div class="profile-subTitle">
                            안전한 비밀번호로 내 정보를 보호하세요. 이전에 사용한적없는 없는 비밀번호가 안전합니다.
                        </div>
                        <div class="profile-index">
                            **********
                        </div>
                    </div>
                    <div class="profile-right">
                        <div class="fix-btn" onclick="location.href = '/user/modify/pass'">
                            수정하기
                        </div>
                    </div>
                </section>
                {{--<section class="profile-box">--}}
                    {{--<div class="profile-left">--}}
                        {{--<div class="profile-title">--}}
                            {{--<div class="profile-name">--}}
                                {{--비밀번호 질문--}}
                            {{--</div>--}}
                            {{--<div class="profile-subName">--}}
                                {{--Password Question--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="profile-subTitle">--}}
                            {{--비밀번호 질문을 설정하시면 추후에 계정을 더안전하게 보호할수있습니다.--}}
                        {{--</div>--}}
                        {{--<div class="profile-index">--}}
                            {{--질의문 없음--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="profile-right">--}}
                        {{--<div class="fix-btn">--}}
                            {{--추가하기--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</section>--}}
                <section class="profile-box">
                    <div class="profile-left">
                        <div class="profile-title">
                            <div class="profile-name">
                                이메일 (미인증)
                            </div>
                            <div class="profile-subName">
                                E-mail
                            </div>
                        </div>
                        <div class="profile-subTitle">
                            아이디 비밀번호 찾기 등 본인확인이 필요한 경우에 사용하는 이메일입니다.
                        </div>
                        <div class="profile-index">
                            eunsol2953@naver.com
                        </div>
                    </div>
                    <div class="profile-right">
                        <div class="fix-btn" >
                            수정하기
                        </div>
                    </div>
                    <div class="profile-right">
                        <div class="fix-btn">
                            인증하기
                        </div>
                    </div>
                </section>
                {{--<section class="profile-box">--}}
                    {{--<div class="profile-left">--}}
                        {{--<div class="profile-title">--}}
                            {{--<div class="profile-name">--}}
                                {{--지역--}}
                            {{--</div>--}}
                            {{--<div class="profile-subName">--}}
                                {{--Area--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="profile-subTitle">--}}
                            {{--투어코치에서 공통적으로 사용하는 지역입니다.--}}
                        {{--</div>--}}
                        {{--<div class="profile-index">--}}
                            {{--집 : 서울시 용산구 원효로 97길 33-4 선린인터넷고등학교--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="profile-right">--}}
                        {{--<div class="fix-btn">--}}
                            {{--수정하기--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</section>--}}
            </div>
        </section>
    </div>
    <footer>
        <div class="footer-icon">
            <img src="/img/RESOURCE/Main/ic_airplane.png" alt="" draggable="false">
        </div>
        <div class="footer-logo">
            <img src="/img/RESOURCE/Main/ic_tour_coach.png" alt="" draggable="false">
        </div>
        <div class="null-box">
            <!-- 기둥뒤에 공간있어요 -->
        </div>
        <div class="sk-logo">
            <img src="" alt="" draggable="false">
        </div>
    </footer>
@endsection