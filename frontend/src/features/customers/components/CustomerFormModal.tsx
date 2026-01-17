import { useState, useEffect } from 'react';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { IMaskInput } from 'react-imask';
import { X, AlertCircle } from 'lucide-react';
import customerService, { type Customer } from '../services/customerService';
import segmentService, { type Segment } from '../services/segmentService';
import { customerSchema, type CustomerFormData } from '@/schemas/customerSchema';
import { cleanDocument, formatDocument, formatPhone } from '@/utils/formatters';

interface CustomerFormModalProps {
  customer: Customer | null;
  onClose: () => void;
  onSave: () => void;
}

const CustomerFormModal = ({ customer, onClose, onSave }: CustomerFormModalProps) => {
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [segments, setSegments] = useState<Segment[]>([]);
  const [serverError, setServerError] = useState<string>('');

  const {
    control,
    handleSubmit,
    formState: { errors },
    reset,
  } = useForm<CustomerFormData>({
    resolver: zodResolver(customerSchema),
    defaultValues: {
      name: '',
      document: '',
      email: '',
      phone: '',
      status: 'prospect',
      segment_id: undefined,
    },
  });

  useEffect(() => {
    const loadSegments = async () => {
      try {
        const data = await segmentService.getAll();
        setSegments(data);
      } catch (error) {
        console.error('Erro ao carregar segmentos:', error);
      }
    };
    
    loadSegments();
  }, []);

  useEffect(() => {
    if (customer) {
      reset({
        name: customer.name,
        document: formatDocument(customer.document), // Formata ao carregar
        email: customer.email,
        phone: customer.phone ? formatPhone(customer.phone) : '', // Formata ao carregar
        status: customer.status,
        segment_id: customer.segment?.id,
      });
    }
  }, [customer, reset]);

  const onSubmit = async (data: CustomerFormData) => {
    try {
      setIsSubmitting(true);
      setServerError('');

      // Limpa formatação antes de enviar ao backend
      const dataToSend = {
        ...data,
        document: cleanDocument(data.document),
        phone: data.phone ? cleanDocument(data.phone) : undefined,
        segment_id: data.segment_id || null,
      };

      if (customer) {
        await customerService.update(customer.id, dataToSend);
      } else {
        await customerService.create(dataToSend);
      }

      onSave();
    } catch (error: any) {
      console.error('Erro ao salvar cliente:', error);
      
      if (error.response?.data?.message) {
        setServerError(error.response.data.message);
      } else if (error.response?.data?.errors) {
        // Mapeia erros do Laravel para o formulário
        const backendErrors = error.response.data.errors;
        Object.keys(backendErrors).forEach((key) => {
          setServerError(backendErrors[key][0]);
        });
      } else {
        setServerError('Erro ao salvar cliente. Tente novamente.');
      }
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        {/* Header */}
        <div className="flex items-center justify-between p-6 border-b border-gray-200">
          <h2 className="text-xl font-bold text-gray-900">
            {customer ? 'Editar Cliente' : 'Novo Cliente'}
          </h2>
          <button
            onClick={onClose}
            className="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <X size={24} />
          </button>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit(onSubmit)} className="p-6">
          {/* Erro do servidor */}
          {serverError && (
            <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2">
              <AlertCircle className="text-red-600 mt-0.5" size={18} />
              <p className="text-sm text-red-600">{serverError}</p>
            </div>
          )}

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {/* Nome */}
            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Nome Completo *
              </label>
              <Controller
                name="name"
                control={control}
                render={({ field }) => (
                  <input
                    {...field}
                    type="text"
                    className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors ${
                      errors.name ? 'border-red-500' : 'border-gray-300'
                    }`}
                    placeholder="Ex: João Silva"
                  />
                )}
              />
              {errors.name && (
                <p className="mt-1 text-sm text-red-600 flex items-center gap-1">
                  <AlertCircle size={14} />
                  {errors.name.message}
                </p>
              )}
            </div>

            {/* Documento com máscara dinâmica CPF/CNPJ */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                CPF/CNPJ *
              </label>
              <Controller
                name="document"
                control={control}
                render={({ field }) => (
                  <IMaskInput
                    {...field}
                    mask={[
                      { mask: '000.000.000-00', maxLength: 11 },  // CPF
                      { mask: '00.000.000/0000-00', maxLength: 14 }  // CNPJ
                    ]}
                    onAccept={(value) => field.onChange(value)}
                    className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors ${
                      errors.document ? 'border-red-500' : 'border-gray-300'
                    }`}
                    placeholder="Ex: 123.456.789-00 ou 12.345.678/0001-90"
                  />
                )}
              />
              {errors.document && (
                <p className="mt-1 text-sm text-red-600 flex items-center gap-1">
                  <AlertCircle size={14} />
                  {errors.document.message}
                </p>
              )}
            </div>

            {/* Status */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Status
              </label>
              <Controller
                name="status"
                control={control}
                render={({ field }) => (
                  <select
                    {...field}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="prospect">Prospecto</option>
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                    <option value="churned">Perdido</option>
                  </select>
                )}
              />
            </div>

            {/* Segmento */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Segmento
              </label>
              <Controller
                name="segment_id"
                control={control}
                render={({ field }) => (
                  <select
                    {...field}
                    value={field.value || ''}
                    onChange={(e) => field.onChange(e.target.value ? parseInt(e.target.value) : undefined)}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Selecione...</option>
                    {segments.map((segment) => (
                      <option key={segment.id} value={segment.id}>
                        {segment.name}
                      </option>
                    ))}
                  </select>
                )}
              />
            </div>

            {/* Email */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Email *
              </label>
              <Controller
                name="email"
                control={control}
                render={({ field }) => (
                  <input
                    {...field}
                    type="email"
                    className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors ${
                      errors.email ? 'border-red-500' : 'border-gray-300'
                    }`}
                    placeholder="Ex: joao@empresa.com"
                  />
                )}
              />
              {errors.email && (
                <p className="mt-1 text-sm text-red-600 flex items-center gap-1">
                  <AlertCircle size={14} />
                  {errors.email.message}
                </p>
              )}
            </div>

            {/* Telefone com máscara dinâmica */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Telefone
              </label>
              <Controller
                name="phone"
                control={control}
                render={({ field }) => (
                  <IMaskInput
                    {...field}
                    value={field.value || ''}
                    mask={[
                      { mask: '(00) 0000-0000' },  // Fixo
                      { mask: '(00) 00000-0000' }  // Celular
                    ]}
                    onAccept={(value) => field.onChange(value)}
                    className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors ${
                      errors.phone ? 'border-red-500' : 'border-gray-300'
                    }`}
                    placeholder="Ex: (11) 98765-4321"
                  />
                )}
              />
              {errors.phone && (
                <p className="mt-1 text-sm text-red-600 flex items-center gap-1">
                  <AlertCircle size={14} />
                  {errors.phone.message}
                </p>
              )}
            </div>
          </div>

          {/* Footer */}
          <div className="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
              disabled={isSubmitting}
            >
              Cancelar
            </button>
            <button
              type="submit"
              disabled={isSubmitting}
              className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {isSubmitting ? 'Salvando...' : customer ? 'Atualizar' : 'Criar'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default CustomerFormModal;
