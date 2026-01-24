import { zodResolver } from '@hookform/resolvers/zod';
import { AlertCircle, X } from 'lucide-react';
import { useEffect } from 'react';
import { Controller, useForm } from 'react-hook-form';
import { type ProductCategory } from '../services/categoryService';
import { createProductSchema, type ProductFormData, unitOptions } from '../schemas/productSchema';
import { type Product } from '../services/productService';

interface ProductFormModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (data: ProductFormData) => void;
  product?: Product | null;
  isLoading?: boolean;
  categories: ProductCategory[];
  loadingCategories?: boolean;
}

export default function ProductFormModal({
  isOpen,
  onClose,
  onSubmit,
  product,
  isLoading = false,
  categories,
  loadingCategories = false,
}: ProductFormModalProps) {

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
    control,
  } = useForm({
    resolver: zodResolver(createProductSchema),
    defaultValues: {
      is_active: true,
      requires_approval: false,
    },
  });

  // Reset form when product changes or modal opens
  useEffect(() => {
    if (isOpen) {
      if (product) {
        reset({
          name: product.name,
          sku: product.sku,
          description: product.description || '',
          category_id: product.category?.id,
          base_price: product.base_price,
          cost_price: product.cost_price,
          unit: product.unit,
          is_active: product.is_active,
          requires_approval: product.requires_approval,
        });
      } else {
        reset({
          name: '',
          sku: '',
          description: '',
          category_id: undefined,
          base_price: 0,
          cost_price: undefined,
          unit: 'hour',
          is_active: true,
          requires_approval: false,
        });
      }
    }
  }, [isOpen, product, reset]);

  const handleFormSubmit = (data: ProductFormData) => {
    onSubmit(data);
  };

  const handleClose = () => {
    reset();
    onClose();
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div className="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-lg bg-white shadow-xl">
        {/* Header */}
        <div className="sticky top-0 z-10 flex items-center justify-between border-b bg-white px-6 py-4">
          <h2 className="text-xl font-semibold text-gray-900">
            {product ? 'Editar Produto' : 'Novo Produto'}
          </h2>
          <button
            type="button"
            onClick={handleClose}
            className="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
          >
            <X className="h-5 w-5" />
          </button>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit(handleFormSubmit)} className="p-6">
          <div className="space-y-4">
            {/* Name */}
            <div>
              <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                Nome <span className="text-red-500">*</span>
              </label>
              <input
                {...register('name')}
                type="text"
                id="name"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                placeholder="Ex: Hora de Desenvolvimento Senior"
              />
              {errors.name && (
                <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                  <AlertCircle className="h-4 w-4" />
                  {errors.name.message}
                </p>
              )}
            </div>

            {/* SKU */}
            <div>
              <label htmlFor="sku" className="block text-sm font-medium text-gray-700">
                SKU <span className="text-red-500">*</span>
              </label>
              <input
                {...register('sku')}
                type="text"
                id="sku"
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 uppercase shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                placeholder="Ex: DEV-SENIOR-HOUR"
                style={{ textTransform: 'uppercase' }}
              />
              {errors.sku && (
                <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                  <AlertCircle className="h-4 w-4" />
                  {errors.sku.message}
                </p>
              )}
            </div>

            {/* Description */}
            <div>
              <label htmlFor="description" className="block text-sm font-medium text-gray-700">
                Descrição
              </label>
              <textarea
                {...register('description')}
                id="description"
                rows={3}
                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                placeholder="Descreva o produto/serviço..."
              />
              {errors.description && (
                <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                  <AlertCircle className="h-4 w-4" />
                  {errors.description.message}
                </p>
              )}
            </div>

            {/* Category and Unit - Row */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              {/* Category */}
              <div>
                <label htmlFor="category_id" className="block text-sm font-medium text-gray-700">
                  Categoria
                </label>
                <Controller
                  name="category_id"
                  control={control}
                  render={({ field }) => (
                    <select
                      {...field}
                      id="category_id"
                      value={field.value || ''}
                      onChange={(e) => field.onChange(e.target.value ? Number(e.target.value) : undefined)}
                      disabled={loadingCategories}
                      className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                      <option value="">Selecione...</option>
                      {categories.map((category) => (
                        <option key={category.id} value={category.id}>
                          {category.name}
                        </option>
                      ))}
                    </select>
                  )}
                />
                {errors.category_id && (
                  <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                    <AlertCircle className="h-4 w-4" />
                    {errors.category_id.message}
                  </p>
                )}
              </div>

              {/* Unit */}
              <div>
                <label htmlFor="unit" className="block text-sm font-medium text-gray-700">
                  Unidade <span className="text-red-500">*</span>
                </label>
                <select
                  {...register('unit')}
                  id="unit"
                  className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                >
                  {unitOptions.map((option) => (
                    <option key={option.value} value={option.value}>
                      {option.label}
                    </option>
                  ))}
                </select>
                {errors.unit && (
                  <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                    <AlertCircle className="h-4 w-4" />
                    {errors.unit.message}
                  </p>
                )}
              </div>
            </div>

            {/* Prices - Row */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              {/* Base Price */}
              <div>
                <label htmlFor="base_price" className="block text-sm font-medium text-gray-700">
                  Preço Base (R$) <span className="text-red-500">*</span>
                </label>
                <input
                  {...register('base_price')}
                  type="number"
                  step="0.01"
                  id="base_price"
                  className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                  placeholder="0.00"
                />
                {errors.base_price && (
                  <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                    <AlertCircle className="h-4 w-4" />
                    {errors.base_price.message}
                  </p>
                )}
              </div>

              {/* Cost Price */}
              <div>
                <label htmlFor="cost_price" className="block text-sm font-medium text-gray-700">
                  Preço de Custo (R$)
                </label>
                <input
                  {...register('cost_price')}
                  type="number"
                  step="0.01"
                  id="cost_price"
                  className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                  placeholder="0.00"
                />
                {errors.cost_price && (
                  <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                    <AlertCircle className="h-4 w-4" />
                    {errors.cost_price.message}
                  </p>
                )}
              </div>
            </div>

            {/* Checkboxes */}
            <div className="space-y-2">
              <div className="flex items-center">
                <input
                  {...register('is_active')}
                  type="checkbox"
                  id="is_active"
                  className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <label htmlFor="is_active" className="ml-2 block text-sm text-gray-700">
                  Produto ativo
                </label>
              </div>

              <div className="flex items-center">
                <input
                  {...register('requires_approval')}
                  type="checkbox"
                  id="requires_approval"
                  className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <label htmlFor="requires_approval" className="ml-2 block text-sm text-gray-700">
                  Requer aprovação para venda
                </label>
              </div>
            </div>
          </div>

          {/* Footer */}
          <div className="mt-6 flex justify-end gap-3">
            <button
              type="button"
              onClick={handleClose}
              disabled={isLoading}
              className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
            >
              Cancelar
            </button>
            <button
              type="submit"
              disabled={isLoading}
              className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
            >
              {isLoading ? 'Salvando...' : product ? 'Atualizar' : 'Criar'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
