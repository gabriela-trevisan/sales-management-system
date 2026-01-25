import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from '@/contexts/AuthContext';
import { LoginPage } from '@/features/auth/pages/LoginPage';
import { PrivateRoute } from '@/features/auth/components/PrivateRoute';
import { Layout } from '@/components/layout/Layout';
import { DashboardPage } from '@/features/dashboard/pages/DashboardPage';
import CustomerListPage from '@/features/customers/pages/CustomerListPage';
import ProductListPage from '@/features/products/pages/ProductListPage';
import ProposalListPage from '@/features/proposals/pages/ProposalListPage';
import ProposalViewPage from '@/features/proposals/pages/ProposalViewPage';

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
          
          <Route
            path="/products"
            element={
              <PrivateRoute>
                <Layout>
                  <ProductListPage />
                </Layout>
              </PrivateRoute>
            }
          />
          
          <Route
            path="/proposals"
            element={
              <PrivateRoute>
                <Layout>
                  <ProposalListPage />
                </Layout>
              </PrivateRoute>
            }
          />
          
          <Route
            path="/proposals/:id"
            element={
              <PrivateRoute>
                <Layout>
                  <ProposalViewPage />
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
