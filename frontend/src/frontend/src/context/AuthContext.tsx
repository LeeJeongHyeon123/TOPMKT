// 인증 상태 관리를 위한 React Context
import React, { createContext, useContext, useReducer, useEffect, ReactNode } from 'react'
import { User } from '@/types'
import AuthService from '@/services/authService'

// 인증 상태 타입 정의
interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
}

// 액션 타입 정의
type AuthAction =
  | { type: 'AUTH_START' }
  | { type: 'AUTH_SUCCESS'; payload: User }
  | { type: 'AUTH_ERROR'; payload: string }
  | { type: 'AUTH_LOGOUT' }
  | { type: 'CLEAR_ERROR' }
  | { type: 'UPDATE_USER'; payload: Partial<User> };

// 초기 상태
const initialState: AuthState = {
  user: null,
  isAuthenticated: false,
  isLoading: true,
  error: null,
};

// 리듀서
const authReducer = (state: AuthState, action: AuthAction): AuthState => {
  switch (action.type) {
    case 'AUTH_START':
      return {
        ...state,
        isLoading: true,
        error: null,
      };
    
    case 'AUTH_SUCCESS':
      return {
        ...state,
        user: action.payload,
        isAuthenticated: true,
        isLoading: false,
        error: null,
      };
    
    case 'AUTH_ERROR':
      return {
        ...state,
        user: null,
        isAuthenticated: false,
        isLoading: false,
        error: action.payload,
      };
    
    case 'AUTH_LOGOUT':
      return {
        ...state,
        user: null,
        isAuthenticated: false,
        isLoading: false,
        error: null,
      };
    
    case 'CLEAR_ERROR':
      return {
        ...state,
        error: null,
      };
    
    case 'UPDATE_USER':
      return {
        ...state,
        user: state.user ? { ...state.user, ...action.payload } : null,
      };
    
    default:
      return state;
  }
};

// Context 타입 정의
interface AuthContextType extends AuthState {
  login: (phone: string, password: string, remember?: boolean) => Promise<void>;
  logout: () => Promise<void>;
  signup: (userData: any) => Promise<void>;
  updateUser: (userData: Partial<User>) => void;
  clearError: () => void;
  checkAuth: () => Promise<void>;
}

// Context 생성
const AuthContext = createContext<AuthContextType | undefined>(undefined);

// Provider 컴포넌트
interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [state, dispatch] = useReducer(authReducer, initialState);

  // 앱 시작 시 인증 상태 확인
  useEffect(() => {
    checkAuth();
  }, []);

  // 인증 상태 확인
  const checkAuth = async () => {
    try {
      const token = AuthService.getToken();
      console.log('AuthContext - checkAuth - token:', token);
      
      if (!token) {
        console.log('AuthContext - No token found, setting logout state');
        dispatch({ type: 'AUTH_LOGOUT' });
        return;
      }

      // 먼저 저장된 사용자 정보가 있는지 확인
      const storedUser = AuthService.getStoredUser();
      if (storedUser) {
        console.log('AuthContext - Using stored user data for immediate auth');
        dispatch({ type: 'AUTH_SUCCESS', payload: storedUser });
        return;
      }

      dispatch({ type: 'AUTH_START' });
      
      try {
        const response = await AuthService.getCurrentUser();
        console.log('AuthContext - getCurrentUser response:', response);
        
        if (response.success && response.data) {
          console.log('AuthContext - Setting auth success with user:', response.data);
          dispatch({ type: 'AUTH_SUCCESS', payload: response.data });
        } else {
          console.log('AuthContext - getCurrentUser failed, setting logout');
          dispatch({ type: 'AUTH_LOGOUT' });
        }
      } catch (apiError) {
        console.log('AuthContext - getCurrentUser API error, using token fallback:', apiError);
        // API 실패 시에도 토큰이 있으면 인증된 상태로 유지
        if (token) {
          console.log('AuthContext - Token exists, maintaining auth state');
          // 기본 사용자 객체로 설정
          const fallbackUser: User = { 
            id: 0, 
            nickname: '사용자', 
            phone: '', 
            email: '',
            role: 'ROLE_USER',
            marketing_agreed: false,
            phone_verified: false,
            email_verified: false,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
          };
          dispatch({ type: 'AUTH_SUCCESS', payload: fallbackUser });
        } else {
          dispatch({ type: 'AUTH_LOGOUT' });
        }
      }
    } catch (error) {
      console.log('AuthContext - Unexpected error:', error);
      dispatch({ type: 'AUTH_LOGOUT' });
    }
  };

  // 로그인
  const login = async (phone: string, password: string, remember = false) => {
    try {
      dispatch({ type: 'AUTH_START' });
      
      const response = await AuthService.login({ phone, password, remember });
      
      if (response.success && response.data?.user) {
        dispatch({ type: 'AUTH_SUCCESS', payload: response.data.user });
      } else {
        throw new Error(response.message || '로그인에 실패했습니다.');
      }
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : '로그인에 실패했습니다.';
      dispatch({ type: 'AUTH_ERROR', payload: errorMessage });
      throw error;
    }
  };

  // 로그아웃
  const logout = async () => {
    try {
      await AuthService.logout();
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      dispatch({ type: 'AUTH_LOGOUT' });
    }
  };

  // 회원가입
  const signup = async (userData: any) => {
    try {
      dispatch({ type: 'AUTH_START' });
      
      const response = await AuthService.signup(userData);
      
      if (response.success && response.data?.user) {
        dispatch({ type: 'AUTH_SUCCESS', payload: response.data.user });
      } else {
        throw new Error(response.message || '회원가입에 실패했습니다.');
      }
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : '회원가입에 실패했습니다.';
      dispatch({ type: 'AUTH_ERROR', payload: errorMessage });
      throw error;
    }
  };

  // 사용자 정보 업데이트 (로컬 상태만)
  const updateUser = (userData: Partial<User>) => {
    dispatch({ type: 'UPDATE_USER', payload: userData });
  };

  // 에러 클리어
  const clearError = () => {
    dispatch({ type: 'CLEAR_ERROR' });
  };

  const value: AuthContextType = {
    ...state,
    login,
    logout,
    signup,
    updateUser,
    clearError,
    checkAuth,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};

// Hook
export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

export default AuthContext;