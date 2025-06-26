import React from 'react';
import { usePageMeta } from '../hooks/usePageMeta';
import SEOHead from '../components/common/SEOHead';

const TermsPage: React.FC = () => {
  // SEO 메타 데이터
  const metaData = usePageMeta({
    title: '이용약관',
    description: '탑마케팅 서비스 이용약관을 확인하세요',
    ogType: 'website'
  });

  return (
    <>
      <SEOHead {...metaData} />
      
      <div className="legal-page">
        <div className="legal-container">
          <div className="legal-header">
            <h1>이용약관</h1>
            <p>탑마케팅 서비스를 이용하시기 전에 반드시 읽어보시기 바랍니다.</p>
            <div className="update-info">
              <span>최종 업데이트: 2024년 1월 1일</span>
            </div>
          </div>

          <div className="legal-content">
            <section className="legal-section">
              <h2>제1조 (목적)</h2>
              <p>
                이 약관은 탑마케팅(이하 "회사")이 제공하는 네트워크 마케팅 플랫폼 서비스(이하 "서비스")의 이용과 관련하여 
                회사와 이용자간의 권리, 의무 및 책임사항, 기타 필요한 사항을 규정함을 목적으로 합니다.
              </p>
            </section>

            <section className="legal-section">
              <h2>제2조 (정의)</h2>
              <p>이 약관에서 사용하는 용어의 정의는 다음과 같습니다:</p>
              <ol>
                <li>"서비스"라 함은 회사가 제공하는 네트워크 마케팅 관련 온라인 플랫폼 서비스를 의미합니다.</li>
                <li>"이용자"라 함은 이 약관에 따라 회사가 제공하는 서비스를 받는 회원 및 비회원을 의미합니다.</li>
                <li>"회원"이라 함은 회사에 개인정보를 제공하여 회원등록을 한 자로서, 회사의 정보를 지속적으로 제공받으며, 회사가 제공하는 서비스를 계속적으로 이용할 수 있는 자를 의미합니다.</li>
                <li>"비회원"이라 함은 회원에 가입하지 않고 회사가 제공하는 서비스를 이용하는 자를 의미합니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제3조 (약관의 공시 및 효력과 변경)</h2>
              <ol>
                <li>이 약관은 서비스 화면에 게시하거나 기타의 방법으로 이용자에게 공지함으로써 효력을 발생합니다.</li>
                <li>회사는 합리적인 사유가 발생할 경우에는 이 약관을 변경할 수 있으며, 약관이 변경된 경우에는 지체 없이 공지합니다.</li>
                <li>이용자는 변경된 약관에 동의하지 않을 경우 서비스 이용을 중단하고 탈퇴할 수 있습니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제4조 (서비스의 제공 및 변경)</h2>
              <ol>
                <li>회사는 다음과 같은 업무를 수행합니다:
                  <ul>
                    <li>네트워크 마케팅 관련 정보 제공</li>
                    <li>커뮤니티 서비스 제공</li>
                    <li>교육 및 강의 서비스 제공</li>
                    <li>이벤트 및 네트워킹 기회 제공</li>
                    <li>기타 회사가 정하는 업무</li>
                  </ul>
                </li>
                <li>회사는 서비스의 내용 및 제공일자를 변경할 수 있으며, 이 경우 사전에 공지합니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제5조 (서비스의 중단)</h2>
              <ol>
                <li>회사는 컴퓨터 등 정보통신설비의 보수점검, 교체 및 고장, 통신의 두절 등의 사유가 발생한 경우에는 서비스의 제공을 일시적으로 중단할 수 있습니다.</li>
                <li>회사는 제1항의 사유로 서비스의 제공이 일시적으로 중단됨으로 인하여 이용자 또는 제3자가 입은 손해에 대하여 배상하지 않습니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제6조 (회원가입)</h2>
              <ol>
                <li>이용자는 회사가 정한 가입 양식에 따라 회원정보를 기입한 후 이 약관에 동의한다는 의사표시를 함으로서 회원가입을 신청합니다.</li>
                <li>회사는 제1항과 같이 회원으로 가입할 것을 신청한 이용자 중 다음 각호에 해당하지 않는 한 회원으로 등록합니다:
                  <ul>
                    <li>가입신청자가 이 약관에 의하여 이전에 회원자격을 상실한 적이 있는 경우</li>
                    <li>등록 내용에 허위, 기재누락, 오기가 있는 경우</li>
                    <li>기타 회원으로 등록하는 것이 회사의 기술상 현저히 지장이 있다고 판단되는 경우</li>
                  </ul>
                </li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제7조 (회원 탈퇴 및 자격 상실)</h2>
              <ol>
                <li>회원은 회사에 언제든지 탈퇴를 요청할 수 있으며 회사는 즉시 회원탈퇴를 처리합니다.</li>
                <li>회원이 다음 각호의 사유에 해당하는 경우, 회사는 회원자격을 제한 및 정지시킬 수 있습니다:
                  <ul>
                    <li>가입 신청 시에 허위 내용을 등록한 경우</li>
                    <li>다른 사람의 서비스 이용을 방해하거나 그 정보를 도용하는 등 전자상거래 질서를 위협하는 경우</li>
                    <li>서비스를 이용하여 법령 또는 이 약관이 금지하거나 공서양속에 반하는 행위를 하는 경우</li>
                  </ul>
                </li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제8조 (회원에 대한 통지)</h2>
              <ol>
                <li>회사가 회원에 대한 통지를 하는 경우, 회원이 회사와 미리 약정하여 지정한 전자우편 주소로 할 수 있습니다.</li>
                <li>회사는 불특정다수 회원에 대한 통지의 경우 1주일이상 회사 게시판에 게시함으로서 개별 통지에 갈음할 수 있습니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제9조 (개인정보보호)</h2>
              <ol>
                <li>회사는 이용자의 개인정보 수집시 서비스제공을 위하여 필요한 범위에서 최소한의 개인정보를 수집합니다.</li>
                <li>회사는 회원가입시 구매계약이행에 필요한 정보를 미리 수집하지 않습니다.</li>
                <li>회사는 이용자의 개인정보를 수집·이용하는 때에는 당해 이용자에게 그 목적을 고지하고 동의를 받습니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제10조 (회사의 의무)</h2>
              <ol>
                <li>회사는 법령과 이 약관이 금지하거나 공서양속에 반하는 행위를 하지 않으며 이 약관이 정하는 바에 따라 지속적이고, 안정적으로 서비스를 제공하는데 최선을 다하여야 합니다.</li>
                <li>회사는 이용자가 안전하게 인터넷 서비스를 이용할 수 있도록 이용자의 개인정보보호를 위한 보안 시스템을 구축하여야 합니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제11조 (회원의 의무)</h2>
              <p>회원은 다음 행위를 하여서는 안 됩니다:</p>
              <ol>
                <li>신청 또는 변경시 허위 내용의 등록</li>
                <li>타인의 정보 도용</li>
                <li>회사가 게시한 정보의 변경</li>
                <li>회사가 정한 정보 이외의 정보(컴퓨터 프로그램 등) 등의 송신 또는 게시</li>
                <li>회사 기타 제3자의 저작권 등 지적재산권에 대한 침해</li>
                <li>회사 기타 제3자의 명예를 손상시키거나 업무를 방해하는 행위</li>
                <li>외설 또는 폭력적인 메시지, 화상, 음성, 기타 공서양속에 반하는 정보를 서비스에 공개 또는 게시하는 행위</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제12조 (저작권의 귀속 및 이용제한)</h2>
              <ol>
                <li>회사가 작성한 저작물에 대한 저작권 기타 지적재산권은 회사에 귀속합니다.</li>
                <li>이용자는 서비스를 이용함으로써 얻은 정보 중 회사에게 지적재산권이 귀속된 정보를 회사의 사전 승낙 없이 복제, 송신, 출판, 배포, 방송 기타 방법에 의하여 영리목적으로 이용하거나 제3자에게 이용하게 하여서는 안됩니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제13조 (분쟁해결)</h2>
              <ol>
                <li>회사는 이용자가 제기하는 정당한 의견이나 불만을 반영하고 그 피해를 보상처리하기 위하여 피해보상처리기구를 설치·운영합니다.</li>
                <li>회사는 이용자로부터 제출되는 불만사항 및 의견은 우선적으로 그 사항을 처리합니다.</li>
                <li>회사와 이용자 간에 발생한 분쟁은 전자거래조정위원회 또는 소비자분쟁조정위원회의 조정에 따를 수 있습니다.</li>
              </ol>
            </section>

            <section className="legal-section">
              <h2>제14조 (재판권 및 준거법)</h2>
              <ol>
                <li>회사와 이용자 간에 발생한 분쟁에 관한 소송은 민사소송법상의 관할법원에 제기합니다.</li>
                <li>회사와 이용자 간에 제기된 소송에는 한국법을 적용합니다.</li>
              </ol>
            </section>

            <div className="legal-footer">
              <p>
                <strong>부칙</strong><br />
                이 약관은 2024년 1월 1일부터 적용됩니다.
              </p>
              <div className="contact-info">
                <h3>문의사항</h3>
                <p>
                  이용약관에 대한 문의사항이 있으시면 언제든지 연락주세요.<br />
                  이메일: support@topmarketing.com<br />
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

          .contact-info {
            padding: 1.5rem;
          }
        }
      `}</style>
    </>
  );
};

export default TermsPage;