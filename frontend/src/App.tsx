import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from '@/contexts/AuthContext';
import { LoginPage } from '@/features/auth/pages/LoginPage';
import { PrivateRoute } from '@/features/auth/components/PrivateRoute';
import { Layout } from '@/components/layout/Layout';
import { DashboardPage } from '@/features/dashboard/pages/DashboardPage';
import CustomerListPage from '@/features/customers/pages/CustomerListPage';

function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>
          <Route path="/login" element={<LoginPage />} />
          
          <Route
            path="/dashboard"
            element={
              <PrivateRoute>
                <Layout>
                  <DashboardPage />
                </Layout>
              </PrivateRoute>
            }
          />
          
          <Route
            path="/customers"
            element={
              <PrivateRoute>
                <Layout>
                  <CustomerListPage />
                </Layout>
              </PrivateRoute>
            }
          />
          
          <Route path="/" element={<Navigate to="/dashboard" replace />} />
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  );
}

export default App;
