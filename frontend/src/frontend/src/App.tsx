import { Routes, Route, Navigate } from 'react-router-dom'
import Layout from '@/components/common/Layout'
import ProtectedRoute from '@/components/common/ProtectedRoute'
import ToastContainer from '@/components/common/ToastContainer'
import ScrollToTop from '@/components/common/ScrollToTop'
import ErrorBoundary from '@/components/common/ErrorBoundary'
import { LoadingEventHandler } from '@/context/LoadingContext'

// Pages
import HomePage from '@/pages/HomePage'
import LoginPage from '@/pages/auth/LoginPage'
import SignupPage from '@/pages/auth/SignupPage'
import ForgotPasswordPage from '@/pages/auth/ForgotPasswordPage'
import LecturesPage from '@/pages/lectures/LecturesPage'
import LectureDetailPage from '@/pages/lectures/LectureDetailPage'
import CommunityPage from '@/pages/community/CommunityPage'
import PostDetailPage from '@/pages/community/PostDetailPage'
import PostWritePage from '@/pages/community/PostWritePage'
import EventsPage from '@/pages/events/EventsPage'
import ProfilePage from '@/pages/profile/ProfilePage'
import PublicProfilePage from '@/pages/profile/PublicProfilePage'
import MyPage from '@/pages/profile/MyPage'
import EditProfilePage from '@/pages/profile/EditProfilePage'
import UserProfilePage from '@/pages/user/UserProfilePage'

// Admin Pages
import AdminDashboard from '@/pages/admin/AdminDashboard'
import UserManagement from '@/pages/admin/UserManagement'

function App() {
  return (
    <ErrorBoundary>
      <ScrollToTop />
      <Routes>
        {/* All routes with layout */}
        <Route path="/" element={<Layout />}>
          {/* Public routes */}
          <Route index element={<HomePage />} />
          <Route path="login" element={<LoginPage />} />
          <Route path="signup" element={<SignupPage />} />
          <Route path="forgot-password" element={<ForgotPasswordPage />} />
          
          {/* Other public routes */}
          <Route path="lectures" element={<LecturesPage />} />
          <Route path="lectures/:id" element={<LectureDetailPage />} />
          <Route path="community" element={<CommunityPage />} />
          <Route path="community/post/:id" element={<PostDetailPage />} />
          <Route path="events" element={<EventsPage />} />
          <Route path="profile" element={<ProfilePage />} />
          <Route path="profile/:nickname" element={<PublicProfilePage />} />
          <Route path="user/:nickname" element={<UserProfilePage />} />
          
          {/* Protected routes */}
          <Route 
            path="community/write" 
            element={
              <ProtectedRoute>
                <PostWritePage />
              </ProtectedRoute>
            } 
          />
          <Route path="my" element={
            <ProtectedRoute>
              <MyPage />
            </ProtectedRoute>
          } />
          <Route path="profile/edit" element={
            <ProtectedRoute>
              <EditProfilePage />
            </ProtectedRoute>
          } />
          
          {/* Admin routes - protected */}
          <Route path="admin" element={
            <ProtectedRoute requiredRole="ROLE_ADMIN">
              <AdminDashboard />
            </ProtectedRoute>
          } />
          <Route path="admin/users" element={
            <ProtectedRoute requiredRole="ROLE_ADMIN">
              <UserManagement />
            </ProtectedRoute>
          } />
        </Route>
        
        {/* Fallback route */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
      
      <ToastContainer />
      <LoadingEventHandler />
    </ErrorBoundary>
  )
}

export default App;