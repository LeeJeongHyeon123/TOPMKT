import React from 'react';
import { usePageMeta } from '../hooks/usePageMeta';
import SEOHead from '../components/common/SEOHead';

const PrivacyPage: React.FC = () => {
  // SEO 메타 데이터
  const metaData = usePageMeta({
    title: '개인정보처리방침',
    description: '탑마케팅 개인정보처리방침을 확인하세요',
    ogType: 'website'
  });

  return (
    <>
      <SEOHead {...metaData} />
      
      <div className="legal-page">
        <div className="legal-container">
          <div className="legal-header">
            <h1>개인정보처리방침</h1>
            <p>탑마케팅은 이용자의 개인정보를 중요하게 생각하며, 안전하게 보호하고 있습니다.</p>
            <div className="update-info">
              <span>최종 업데이트: 2024년 1월 1일</span>
            </div>
          </div>

          <div className="legal-content">
            <section className="legal-section">
              <h2>제1조 (개인정보의 처리목적)</h2>
              <p>탑마케팅(이하 "회사")은 다음의 목적을 위하여 개인정보를 처리합니다. 처리하고 있는 개인정보는 다음의 목적 이외의 용도로는 이용되지 않으며, 이용 목적이 변경되는 경우에는 개인정보보호법 제18조에 따라 별도의 동의를 받는 등 필요한 조치를 이행할 예정입니다.</p>
              
              <h3>1. 홈페이지 회원가입 및 관리</h3>
              <p>회원 가입의사 확인, 회원제 서비스 제공에 따른 본인 식별·인증, 회원자격 유지·관리, 서비스 부정이용 방지, 각종 고지·통지, 고충처리 목적으로 개인정보를 처리합니다.</p>
              
              <h3>2. 재화 또는 서비스 제공</h3>
              <p>서비스 제공, 콘텐츠 제공, 맞춤 서비스 제공, 본인인증, 연령인증, 요금결제·정산을 목적으로 개인정보를 처리합니다.</p>
              
              <h3>3. 마케팅 및 광고에의 활용</h3>
              <p>신규 서비스(제품) 개발 및 맞춤 서비스 제공, 이벤트 및 광고성 정보 제공 및 참여기회 제공, 인구통계학적 특성에 따른 서비스 제공 및 광고 게재, 서비스의 유효성 확인, 접속빈도 파악 또는 회원의 서비스 이용에 대한 통계 등을 목적으로 개인정보를 처리합니다.</p>
            </section>

            <section className="legal-section">
              <h2>제2조 (개인정보의 처리 및 보유기간)</h2>
              <ol>
                <li>회사는 법령에 따른 개인정보 보유·이용기간 또는 정보주체로부터 개인정보를 수집 시에 동의받은 개인정보 보유·이용기간 내에서 개인정보를 처리·보유합니다.</li>
                <li>각각의 개인정보 처리 및 보유 기간은 다음과 같습니다:
                  <ul>
                    <li>홈페이지 회원가입 및 관리: 회원 탈퇴 시까지</li>
                    <li>재화 또는 서비스 제공: 재화·서비스 공급완료 및 요금결제·정산 완료시까지</li>
                    <li>마케팅 및 광고에의 활용: 동의철회 시까지</li>
                  </ul>
                </li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제3조 (개인정보의 제3자 제공)</h2>
              <ol>
                <li>회사는 정보주체의 개인정보를 제1조(개인정보의 처리목적)에서 명시한 범위 내에서만 처리하며, 정보주체의 동의, 법률의 특별한 규정 등 개인정보보호법 제17조 및 제18조에 해당하는 경우에만 개인정보를 제3자에게 제공합니다.</li>
                <li>회사는 다음과 같이 개인정보를 제3자에게 제공하고 있습니다:
                  <ul>
                    <li>제공받는 자: 결제대행업체</li>
                    <li>제공받는 자의 개인정보 이용목적: 결제 처리</li>
                    <li>제공하는 개인정보 항목: 이름, 연락처, 결제정보</li>
                    <li>제공받는 자의 보유·이용기간: 결제 완료 후 5년</li>
                  </ul>
                </li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제4조 (개인정보처리의 위탁)</h2>
              <ol>
                <li>회사는 원활한 개인정보 업무처리를 위하여 다음과 같이 개인정보 처리업무를 위탁하고 있습니다:
                  <ul>
                    <li>위탁받는 자(수탁자): 클라우드 서비스 제공업체</li>
                    <li>위탁하는 업무의 내용: 개인정보가 포함된 데이터의 보관</li>
                  </ul>
                </li>
                <li>회사는 위탁계약 체결시 개인정보보호법 제26조에 따라 위탁업무 수행목적 외 개인정보 처리금지, 기술적·관리적 보호조치, 재위탁 제한, 수탁자에 대한 관리·감독, 손해배상 등 책임에 관한 사항을 계약서 등 문서에 명시하고, 수탁자가 개인정보를 안전하게 처리하는지를 감독하고 있습니다.</li>
                <li>위탁업무의 내용이나 수탁자가 변경될 경우에는 지체없이 본 개인정보 처리방침을 통하여 공개하도록 하겠습니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제5조 (정보주체의 권리·의무 및 행사방법)</h2>
              <ol>
                <li>정보주체는 회사에 대해 언제든지 다음 각 호의 개인정보 보호 관련 권리를 행사할 수 있습니다:
                  <ul>
                    <li>개인정보 처리현황 통지요구</li>
                    <li>개인정보 열람요구</li>
                    <li>개인정보 정정·삭제요구</li>
                    <li>개인정보 처리정지요구</li>
                  </ul>
                </li>
                <li>제1항에 따른 권리 행사는 회사에 대해 개인정보보호법 시행령 제41조제1항에 따라 서면, 전자우편, 모사전송(FAX) 등을 통하여 하실 수 있으며 회사는 이에 대해 지체없이 조치하겠습니다.</li>
                <li>정보주체가 개인정보의 오류 등에 대한 정정 또는 삭제를 요구한 경우에는 회사는 정정 또는 삭제를 완료할 때까지 당해 개인정보를 이용하거나 제공하지 않습니다.</li>
                <li>제1항에 따른 권리 행사는 정보주체의 법정대리인이나 위임을 받은 자 등 대리인을 통하여 하실 수 있습니다. 이 경우 개인정보보호법 시행규칙 별지 제11호 서식에 따른 위임장을 제출하셔야 합니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제6조 (처리하는 개인정보 항목)</h2>
              <p>회사는 다음의 개인정보 항목을 처리하고 있습니다:</p>
              
              <h3>1. 홈페이지 회원가입 및 관리</h3>
              <ul>
                <li>필수항목: 이메일, 비밀번호, 닉네임, 휴대전화번호</li>
                <li>선택항목: 프로필 이미지, 자기소개, 생년월일, 성별, 관심분야</li>
              </ul>
              
              <h3>2. 인터넷 서비스 이용과정에서 아래 개인정보 항목이 자동으로 생성되어 수집될 수 있습니다</h3>
              <ul>
                <li>IP주소, 쿠키, MAC주소, 서비스 이용기록, 방문기록, 불량 이용기록 등</li>
              </ul>
            </section>

            <section className="legal-section">
              <h2>제7조 (개인정보의 파기)</h2>
              <ol>
                <li>회사는 개인정보 보유기간의 경과, 처리목적 달성 등 개인정보가 불필요하게 되었을 때에는 지체없이 해당 개인정보를 파기합니다.</li>
                <li>정보주체로부터 동의받은 개인정보 보유기간이 경과하거나 처리목적이 달성되었음에도 불구하고 다른 법령에 따라 개인정보를 계속 보존하여야 하는 경우에는, 해당 개인정보를 별도의 데이터베이스(DB)로 옮기거나 보관장소를 달리하여 보존합니다.</li>
                <li>개인정보 파기의 절차 및 방법은 다음과 같습니다:
                  <ul>
                    <li>파기절차: 회사는 파기 사유가 발생한 개인정보를 선정하고, 회사의 개인정보 보호책임자의 승인을 받아 개인정보를 파기합니다.</li>
                    <li>파기방법: 전자적 파일 형태의 정보는 기록을 재생할 수 없는 기술적 방법을 사용합니다.</li>
                  </ul>
                </li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제8조 (개인정보의 안전성 확보조치)</h2>
              <p>회사는 개인정보보호법 제29조에 따라 다음과 같이 안전성 확보에 필요한 기술적/관리적 및 물리적 조치를 하고 있습니다:</p>
              <ol>
                <li>개인정보 취급 직원의 최소화 및 교육</li>
                <li>개인정보에 대한 접근 제한</li>
                <li>개인정보를 안전하게 저장·전송할 수 있는 암호화 기법 사용</li>
                <li>해킹 등에 대비한 기술적 대책</li>
                <li>개인정보처리시스템 등의 접근권한 관리</li>
                <li>접근통제시스템 설치 및 운영</li>
                <li>개인정보 보관시설의 물리적 보안</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제9조 (개인정보 자동 수집 장치의 설치·운영 및 거부에 관한 사항)</h2>
              <ol>
                <li>회사는 이용자에게 개별적인 맞춤서비스를 제공하기 위해 이용정보를 저장하고 수시로 불러오는 '쿠키(cookie)'를 사용합니다.</li>
                <li>쿠키는 웹사이트를 운영하는데 이용되는 서버(http)가 이용자의 컴퓨터 브라우저에게 보내는 소량의 정보이며 이용자들의 PC 컴퓨터내의 하드디스크에 저장되기도 합니다.</li>
                <li>쿠키의 사용목적: 이용자가 방문한 각 서비스와 웹 사이트들에 대한 방문 및 이용형태, 인기 검색어, 보안접속 여부, 등을 파악하여 이용자에게 최적화된 정보 제공을 위해 사용됩니다.</li>
                <li>쿠키의 설치·운영 및 거부: 웹브라우저 상단의 도구 &gt; 인터넷 옵션 &gt; 개인정보 메뉴의 옵션 설정을 통해 쿠키 저장을 거부할 수 있습니다.</li>
                <li>쿠키 저장을 거부할 경우 맞춤형 서비스 이용에 어려움이 발생할 수 있습니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제10조 (개인정보 보호책임자)</h2>
              <p>회사는 개인정보 처리에 관한 업무를 총괄해서 책임지고, 개인정보 처리와 관련한 정보주체의 불만처리 및 피해구제 등을 위하여 아래와 같이 개인정보 보호책임자를 지정하고 있습니다:</p>
              
              <div className="contact-box">
                <h3>개인정보 보호책임자</h3>
                <ul>
                  <li>성명: 개인정보 보호책임자</li>
                  <li>직책: CTO</li>
                  <li>연락처: privacy@topmarketing.com</li>
                  <li>전화: 1588-0000</li>
                </ul>
              </div>
              
              <p>정보주체께서는 회사의 서비스를 이용하시면서 발생한 모든 개인정보 보호 관련 문의, 불만처리, 피해구제 등에 관한 사항을 개인정보 보호책임자에게 문의하실 수 있습니다. 회사는 정보주체의 문의에 대해 지체없이 답변 및 처리해드릴 것입니다.</p>
            </section>

            <section className="legal-section">
              <h2>제11조 (권익침해 구제방법)</h2>
              <p>정보주체는 아래의 기관에 대해 개인정보 침해에 대한 신고나 상담을 하실 수 있습니다:</p>
              <ul>
                <li>개인정보 침해신고센터 (privacy.go.kr / 전화: 02-2100-2600)</li>
                <li>개인정보 분쟁조정위원회 (www.kopico.go.kr / 전화: 1833-6972)</li>
                <li>대검찰청 사이버범죄수사단 (www.spo.go.kr / 전화: 02-3480-3573)</li>
                <li>경찰청 사이버테러대응센터 (www.netan.go.kr / 전화: 182)</li>
              </ul>
            </section>

            <section className="legal-section">
              <h2>제12조 (개인정보 처리방침 변경)</h2>
              <ol>
                <li>이 개인정보처리방침은 2024년 1월 1일부터 적용됩니다.</li>
                <li>이전의 개인정보 처리방침은 아래에서 확인하실 수 있습니다.</li>
              </ol>
            </section>

            <div className="legal-footer">
              <p>
                <strong>부칙</strong><br />
                이 개인정보처리방침은 2024년 1월 1일부터 적용됩니다.
              </p>
              <div className="contact-info">
                <h3>문의사항</h3>
                <p>
                  개인정보처리방침에 대한 문의사항이 있으시면 언제든지 연락주세요.<br />
                  이메일: privacy@topmarketing.com<br />
                  전화: 1588-0000
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* 법적 문서 페이지 스타일 */}
      <style>{`
        .legal-page {
          background: #f8fafc;
          min-height: calc(100vh - 80px);
          padding: 1rem 0;
        }

        .legal-container {
          max-width: 800px;
          margin: 0 auto;
          padding: 0 1rem;
        }

        .legal-header {
          background: white;
          padding: 3rem 2rem;
          border-radius: 12px;
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
          text-align: center;
          margin-bottom: 2rem;
          border: 1px solid #e2e8f0;
        }

        .legal-header h1 {
          font-size: 2.5rem;
          font-weight: bold;
          color: #1a202c;
          margin-bottom: 1rem;
        }

        .legal-header p {
          font-size: 1.1rem;
          color: #4a5568;
          margin-bottom: 1rem;
        }

        .update-info {
          display: inline-block;
          background: #e6fffa;
          color: #234e52;
          padding: 0.5rem 1rem;
          border-radius: 20px;
          font-size: 0.9rem;
          font-weight: 500;
        }

        .legal-content {
          background: white;
          padding: 3rem;
          border-radius: 12px;
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
          border: 1px solid #e2e8f0;
          line-height: 1.8;
        }

        .legal-section {
          margin-bottom: 3rem;
        }

        .legal-section h2 {
          font-size: 1.4rem;
          font-weight: 600;
          color: #2d3748;
          margin-bottom: 1rem;
          padding-bottom: 0.5rem;
          border-bottom: 2px solid #e2e8f0;
        }

        .legal-section h3 {
          font-size: 1.2rem;
          font-weight: 600;
          color: #2d3748;
          margin: 1.5rem 0 0.75rem 0;
        }

        .legal-section p {
          color: #4a5568;
          margin-bottom: 1rem;
        }

        .legal-section ol {
          color: #4a5568;
          padding-left: 1.5rem;
        }

        .legal-section ol li {
          margin-bottom: 0.75rem;
        }

        .legal-section ul {
          color: #4a5568;
          padding-left: 1.5rem;
          margin-top: 0.5rem;
        }

        .legal-section ul li {
          margin-bottom: 0.5rem;
        }

        .contact-box {
          background: #f7fafc;
          padding: 1.5rem;
          border-radius: 8px;
          border: 1px solid #e2e8f0;
          margin: 1rem 0;
        }

        .contact-box h3 {
          font-size: 1.1rem;
          font-weight: 600;
          color: #2d3748;
          margin-bottom: 1rem;
        }

        .contact-box ul {
          list-style: none;
          padding: 0;
          margin: 0;
        }

        .contact-box ul li {
          margin-bottom: 0.5rem;
          color: #4a5568;
        }

        .legal-footer {
          margin-top: 3rem;
          padding-top: 2rem;
          border-top: 2px solid #e2e8f0;
        }

        .legal-footer p {
          color: #4a5568;
          margin-bottom: 2rem;
        }

        .contact-info {
          background: #f7fafc;
          padding: 2rem;
          border-radius: 8px;
          border: 1px solid #e2e8f0;
        }

        .contact-info h3 {
          font-size: 1.2rem;
          font-weight: 600;
          color: #2d3748;
          margin-bottom: 1rem;
        }

        .contact-info p {
          color: #4a5568;
          margin: 0;
        }

        @media (max-width: 768px) {
          .legal-container {
            padding: 0 0.5rem;
          }

          .legal-header {
            padding: 2rem 1.5rem;
          }

          .legal-header h1 {
            font-size: 2rem;
          }

          .legal-content {
            padding: 2rem 1.5rem;
          }

          .legal-section h2 {
            font-size: 1.2rem;
          }

          .legal-section h3 {
            font-size: 1.1rem;
          }

          .contact-box {
            padding: 1rem;
          }

          .contact-info {
            padding: 1.5rem;
          }
        }
      `}</style>
    </>
  );
};

export default PrivacyPage;