import { zodResolver } from '@hookform/resolvers/zod';
import { AlertCircle, Plus, Trash2, X } from 'lucide-react';
import { useEffect, useMemo } from 'react';
import { Controller, useFieldArray, useForm, useWatch } from 'react-hook-form';
import { type Customer } from '@/features/customers/services/customerService';
import { type Product } from '@/features/products/services/productService';
import { proposalSchema, type ProposalFormData } from '../schemas/proposalSchema';
import { type Proposal } from '../services/proposalService';

interface ProposalFormModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (data: ProposalFormData) => void;
  proposal?: Proposal | null;
  isLoading?: boolean;
  customers: Customer[];
  products: Product[];
  loadingCustomers?: boolean;
  loadingProducts?: boolean;
}

export default function ProposalFormModal({
  isOpen,
  onClose,
  onSubmit,
  proposal,
  isLoading = false,
  customers,
  products,
  loadingCustomers = false,
  loadingProducts = false,
}: ProposalFormModalProps) {
  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
    control,
    setValue,
  } = useForm<ProposalFormData>({
    resolver: zodResolver(proposalSchema),
    defaultValues: {
      status: 'draft',
      items: [{ product_id: 0, quantity: 1, unit_price: 0, discount_percentage: 0 }],
    },
  });

  const { fields, append, remove } = useFieldArray({
    control,
    name: 'items',
  });

  // Watch all items for real-time calculations using useWatch (React Compiler compatible)
  const watchedItems = useWatch({
    control,
    name: 'items',
    defaultValue: [],
  });

  // Calculate totals
  const calculations = useMemo(() => {
    let subtotal = 0;
    let totalDiscount = 0;

    watchedItems?.forEach((item) => {
      const quantity = Number(item.quantity) || 0;
      const unitPrice = Number(item.unit_price) || 0;
      const discountPercentage = Number(item.discount_percentage) || 0;

      const itemSubtotal = quantity * unitPrice;
      const itemDiscount = itemSubtotal * (discountPercentage / 100);

      subtotal += itemSubtotal;
      totalDiscount += itemDiscount;
    });

    const total = subtotal - totalDiscount;

    return {
      subtotal: subtotal.toFixed(2),
      totalDiscount: totalDiscount.toFixed(2),
      total: total.toFixed(2),
    };
  }, [watchedItems]);

  // Reset form when proposal changes or modal opens
  useEffect(() => {
    if (isOpen) {
      if (proposal) {
        reset({
          customer_id: proposal.customer_id,
          opportunity_id: proposal.opportunity_id,
          issue_date: proposal.issue_date,
          expiration_date: proposal.expiration_date,
          notes: proposal.notes || '',
          status: proposal.status,
          items: proposal.items.map((item) => ({
            product_id: item.product_id,
            description: item.description || '',
            quantity: item.quantity,
            unit_price: item.unit_price,
            discount_percentage: item.discount_percentage || 0,
          })),
        });
      } else {
        // Get today's date in YYYY-MM-DD format
        const today = new Date().toISOString().split('T')[0];
        // Get date 30 days from now
        const expiration = new Date();
        expiration.setDate(expiration.getDate() + 30);
        const expirationDate = expiration.toISOString().split('T')[0];

        reset({
          customer_id: 0,
          opportunity_id: undefined,
          issue_date: today,
          expiration_date: expirationDate,
          notes: '',
          status: 'draft',
          items: [{ product_id: 0, quantity: 1, unit_price: 0, discount_percentage: 0 }],
        });
      }
    }
  }, [isOpen, proposal, reset]);

  const handleFormSubmit = (data: ProposalFormData) => {
    onSubmit(data);
  };

  const handleClose = () => {
    reset();
    onClose();
  };

  const handleAddItem = () => {
    append({ product_id: 0, quantity: 1, unit_price: 0, discount_percentage: 0 });
  };

  const handleRemoveItem = (index: number) => {
    if (fields.length > 1) {
      remove(index);
    }
  };

  // Auto-fill unit price when product is selected
  const handleProductChange = (index: number, productId: number) => {
    const selectedProduct = products.find((p) => p.id === productId);
    if (selectedProduct) {
      setValue(`items.${index}.unit_price`, selectedProduct.base_price);
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div className="relative w-full max-w-5xl max-h-[90vh] overflow-y-auto rounded-lg bg-white shadow-xl">
        {/* Header */}
        <div className="sticky top-0 z-10 flex items-center justify-between border-b bg-white px-6 py-4">
          <h2 className="text-xl font-semibold text-gray-900">
            {proposal ? 'Editar Proposta' : 'Nova Proposta'}
          </h2>
          <button
            type="button"
            onClick={handleClose}
            className="rounded-lg p-1 text-muted-foreground hover:bg-muted/50 hover:text-foreground transition-colors"
            disabled={isLoading}
          >
            <X className="h-5 w-5" />
          </button>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit(handleFormSubmit)} className="p-6">
          <div className="space-y-6">
            {/* Basic Information Section */}
            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
              {/* Customer */}
              <div>
                <label htmlFor="customer_id" className="block text-sm font-medium text-gray-700">
                  Cliente <span className="text-red-500">*</span>
                </label>
                <Controller
                  name="customer_id"
                  control={control}
                  render={({ field }) => (
                    <select
                      {...field}
                      id="customer_id"
                      className={`mt-1 block w-full rounded-md border px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 ${
                        errors.customer_id ? 'border-red-300' : 'border-gray-300'
                      }`}
                      disabled={loadingCustomers || isLoading}
                      onChange={(e) => field.onChange(Number(e.target.value))}
                    >
                      <option value={0}>Selecione um cliente</option>
                      {customers.map((customer) => (
                        <option key={customer.id} value={customer.id}>
                          {customer.name} - {customer.document}
                        </option>
                      ))}
                    </select>
                  )}
                />
                {errors.customer_id && (
                  <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                    <AlertCircle className="h-4 w-4" />
                    {errors.customer_id.message}
                  </p>
                )}
              </div>

              {/* Status */}
              <div>
                <label htmlFor="status" className="block text-sm font-medium text-gray-700">
                  Status <span className="text-red-500">*</span>
                </label>
                <select
                  {...register('status')}
                  id="status"
                  className={`mt-1 block w-full rounded-md border px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 ${
                    errors.status ? 'border-red-300' : 'border-gray-300'
                  }`}
                  disabled={isLoading}
                >
                  <option value="draft">Rascunho</option>
                  <option value="sent">Enviada</option>
                  <option value="approved">Aprovada</option>
                  <option value="rejected">Rejeitada</option>
                  <option value="expired">Expirada</option>
                </select>
                {errors.status && (
                  <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                    <AlertCircle className="h-4 w-4" />
                    {errors.status.message}
                  </p>
                )}
              </div>

              {/* Issue Date */}
              <div>
                <label htmlFor="issue_date" className="block text-sm font-medium text-gray-700">
                  Data de Emissão <span className="text-red-500">*</span>
                </label>
                <input
                  {...register('issue_date')}
                  type="date"
                  id="issue_date"
                  className={`mt-1 block w-full rounded-md border px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 ${
                    errors.issue_date ? 'border-red-300' : 'border-gray-300'
                  }`}
                  disabled={isLoading}
                />
                {errors.issue_date && (
                  <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                    <AlertCircle className="h-4 w-4" />
                    {errors.issue_date.message}
                  </p>
                )}
              </div>

              {/* Expiration Date */}
              <div>
                <label htmlFor="expiration_date" className="block text-sm font-medium text-gray-700">
                  Data de Validade <span className="text-red-500">*</span>
                </label>
                <input
                  {...register('expiration_date')}
                  type="date"
                  id="expiration_date"
                  className={`mt-1 block w-full rounded-md border px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 ${
                    errors.expiration_date ? 'border-red-300' : 'border-gray-300'
                  }`}
                  disabled={isLoading}
                />
                {errors.expiration_date && (
                  <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                    <AlertCircle className="h-4 w-4" />
                    {errors.expiration_date.message}
                  </p>
                )}
              </div>
            </div>

            {/* Notes */}
            <div>
              <label htmlFor="notes" className="block text-sm font-medium text-gray-700">
                Observações
              </label>
              <textarea
                {...register('notes')}
                id="notes"
                rows={3}
                className={`mt-1 block w-full rounded-md border px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 ${
                  errors.notes ? 'border-red-300' : 'border-gray-300'
                }`}
                placeholder="Observações adicionais sobre a proposta"
                disabled={isLoading}
              />
              {errors.notes && (
                <p className="mt-1 flex items-center gap-1 text-sm text-red-600">
                  <AlertCircle className="h-4 w-4" />
                  {errors.notes.message}
                </p>
              )}
            </div>

            {/* Items Section */}
            <div className="border-t pt-6">
              <div className="mb-4 flex items-center justify-between">
                <h3 className="text-lg font-medium text-gray-900">
                  Itens da Proposta <span className="text-red-500">*</span>
                </h3>
                <button
                  type="button"
                  onClick={handleAddItem}
                  className="flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                  disabled={isLoading || loadingProducts}
                >
                  <Plus className="h-4 w-4" />
                  Adicionar Item
                </button>
              </div>

              {/* Items Table */}
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-muted/50">
                    <tr>
                      <th className="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Produto
                      </th>
                      <th className="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Descrição
                      </th>
                      <th className="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Qtd
                      </th>
                      <th className="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Preço Unit.
                      </th>
                      <th className="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Desc. %
                      </th>
                      <th className="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Total
                      </th>
                      <th className="px-3 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                        Ações
                      </th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border bg-card">
                    {fields.map((field, index) => {
                      const item = watchedItems?.[index] || field;
                      const quantity = Number(item.quantity) || 0;
                      const unitPrice = Number(item.unit_price) || 0;
                      const discountPercentage = Number(item.discount_percentage) || 0;
                      const itemTotal = quantity * unitPrice * (1 - discountPercentage / 100);

                      return (
                        <tr key={field.id}>
                          {/* Product */}
                          <td className="px-3 py-4">
                            <Controller
                              name={`items.${index}.product_id`}
                              control={control}
                              render={({ field: productField }) => (
                                <select
                                  {...productField}
                                  className={`block w-full min-w-[200px] rounded-md border px-2 py-1 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 ${
                                    errors.items?.[index]?.product_id
                                      ? 'border-red-300'
                                      : 'border-gray-300'
                                  }`}
                                  disabled={loadingProducts || isLoading}
                                  onChange={(e) => {
                                    const productId = Number(e.target.value);
                                    productField.onChange(productId);
                                    handleProductChange(index, productId);
                                  }}
                                >
                                  <option value={0}>Selecione um produto</option>
                                  {products.map((product) => (
                                    <option key={product.id} value={product.id}>
                                      {product.name} - R$ {product.base_price.toFixed(2)}
                                    </option>
                                  ))}
                                </select>
                              )}
                            />
                            {errors.items?.[index]?.product_id && (
                              <p className="mt-1 text-xs text-red-600">
                                {errors.items[index]?.product_id?.message}
                              </p>
                            )}
                          </td>

                          {/* Description */}
                          <td className="px-3 py-4">
                            <input
                              {...register(`items.${index}.description`)}
                              type="text"
                              className="block w-full min-w-[150px] rounded-md border border-gray-300 px-2 py-1 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                              placeholder="Descrição adicional"
                              disabled={isLoading}
                            />
                          </td>

                          {/* Quantity */}
                          <td className="px-3 py-4">
                            <input
                              {...register(`items.${index}.quantity`, { valueAsNumber: true })}
                              type="number"
                              min="1"
                              step="1"
                              className={`block w-20 rounded-md border px-2 py-1 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 ${
                                errors.items?.[index]?.quantity
                                  ? 'border-red-300'
                                  : 'border-gray-300'
                              }`}
                              disabled={isLoading}
                            />
                            {errors.items?.[index]?.quantity && (
                              <p className="mt-1 text-xs text-red-600">
                                {errors.items[index]?.quantity?.message}
                              </p>
                            )}
                          </td>

                          {/* Unit Price */}
                          <td className="px-3 py-4">
                            <input
                              {...register(`items.${index}.unit_price`, { valueAsNumber: true })}
                              type="number"
                              min="0"
                              step="0.01"
                              className={`block w-28 rounded-md border px-2 py-1 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 ${
                                errors.items?.[index]?.unit_price
                                  ? 'border-red-300'
                                  : 'border-gray-300'
                              }`}
                              disabled={isLoading}
                            />
                            {errors.items?.[index]?.unit_price && (
                              <p className="mt-1 text-xs text-red-600">
                                {errors.items[index]?.unit_price?.message}
                              </p>
                            )}
                          </td>

                          {/* Discount Percentage */}
                          <td className="px-3 py-4">
                            <input
                              {...register(`items.${index}.discount_percentage`, {
                                valueAsNumber: true,
                              })}
                              type="number"
                              min="0"
                              max="100"
                              step="0.01"
                              className="block w-20 rounded-md border border-gray-300 px-2 py-1 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                              disabled={isLoading}
                            />
                          </td>

                          {/* Total */}
                          <td className="px-3 py-4">
                            <span className="text-sm font-medium text-gray-900">
                              R$ {itemTotal.toFixed(2)}
                            </span>
                          </td>

                          {/* Actions */}
                          <td className="px-3 py-4 text-center">
                            <button
                              type="button"
                              onClick={() => handleRemoveItem(index)}
                              className="inline-flex items-center rounded p-1 text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                              disabled={fields.length === 1 || isLoading}
                              title="Remover item"
                            >
                              <Trash2 className="h-4 w-4" />
                            </button>
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>

              {errors.items && typeof errors.items.message === 'string' && (
                <p className="mt-2 flex items-center gap-1 text-sm text-red-600">
                  <AlertCircle className="h-4 w-4" />
                  {errors.items.message}
                </p>
              )}
            </div>

            {/* Summary Section */}
            <div className="flex justify-end border-t pt-4">
              <div className="w-full max-w-xs space-y-2">
                <div className="flex justify-between text-sm">
                  <span className="text-gray-600">Subtotal:</span>
                  <span className="font-medium text-gray-900">R$ {calculations.subtotal}</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-600">Desconto:</span>
                  <span className="font-medium text-red-600">- R$ {calculations.totalDiscount}</span>
                </div>
                <div className="flex justify-between border-t pt-2 text-base">
                  <span className="font-semibold text-gray-900">Total:</span>
                  <span className="text-xl font-bold text-blue-600">R$ {calculations.total}</span>
                </div>
              </div>
            </div>
          </div>

          {/* Footer */}
          <div className="mt-6 flex justify-end gap-3 border-t pt-4">
            <button
              type="button"
              onClick={handleClose}
              className="rounded-md border border-border bg-card px-4 py-2 text-sm font-medium text-foreground hover:bg-muted/50 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
              disabled={isLoading}
            >
              Cancelar
            </button>
            <button
              type="submit"
              className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
              disabled={isLoading}
            >
              {isLoading ? 'Salvando...' : proposal ? 'Salvar Alterações' : 'Criar Proposta'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
