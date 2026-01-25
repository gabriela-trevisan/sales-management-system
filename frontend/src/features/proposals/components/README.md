# ProposalFormModal Component

## Descrição
Componente modal completo para criação e edição de propostas comerciais com gestão dinâmica de itens.

## Features Implementadas

### ✅ Formulário Completo
- Seleção de cliente (dropdown com todos os clientes cadastrados)
- Datas de emissão e validade com validação
- Status da proposta (draft, sent, approved, rejected, expired)
- Campo de observações

### ✅ Gestão Dinâmica de Itens
- **Adicionar múltiplos itens** com botão "Adicionar Item"
- **Remover itens** (mínimo de 1 item obrigatório)
- **Auto-preenchimento** do preço unitário ao selecionar produto
- Campos por item:
  - Produto (dropdown com todos os produtos ativos)
  - Descrição adicional (opcional)
  - Quantidade (mínimo 1)
  - Preço unitário (editável)
  - Desconto percentual (0-100%)
  - Total calculado automaticamente

### ✅ Cálculos em Tempo Real
- **Subtotal**: Soma de todos os itens sem desconto
- **Desconto total**: Soma de todos os descontos aplicados
- **Total geral**: Subtotal - Desconto total
- Atualização instantânea ao alterar quantidade, preço ou desconto

### ✅ Validação Completa
- Validação com Zod schema
- Cliente obrigatório
- Data de validade deve ser posterior à data de emissão
- Pelo menos 1 item obrigatório
- Produto obrigatório em cada item
- Quantidade mínima de 1
- Preço unitário >= 0
- Desconto entre 0-100%

### ✅ UX/UI
- Layout responsivo (max-w-5xl)
- Scroll vertical para modal grande
- Loading states durante criação/edição
- Estados disabled durante loading
- Feedback visual de erros
- Tabela com scroll horizontal para muitos itens
- Totalizadores destacados visualmente

## Integração no ProposalListPage

### Mutations Adicionadas
```typescript
// Create mutation
const createMutation = useMutation({
  mutationFn: (data: CreateProposalData) => proposalService.create(data),
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['proposals'] });
    setAlert({ type: 'success', message: 'Proposta criada com sucesso!' });
    setModalState({ isOpen: false, proposal: null });
  },
});

// Update mutation
const updateMutation = useMutation({
  mutationFn: ({ id, data }: { id: number; data: UpdateProposalData }) =>
    proposalService.update(id, data),
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['proposals'] });
    setAlert({ type: 'success', message: 'Proposta atualizada com sucesso!' });
    setModalState({ isOpen: false, proposal: null });
  },
});
```

### Data Fetching
```typescript
// Fetch customers for modal dropdown
const { data: customersData, isLoading: loadingCustomers } = useQuery({
  queryKey: ['customers', { per_page: 1000 }],
  queryFn: () => customerService.getAll({ per_page: 1000 }),
});

// Fetch products for modal dropdown
const { data: productsData, isLoading: loadingProducts } = useQuery({
  queryKey: ['products', { is_active: true, per_page: 1000 }],
  queryFn: () => productService.getAll({ is_active: true, per_page: 1000 }),
});
```

### Modal State Management
```typescript
const [modalState, setModalState] = useState<{
  isOpen: boolean;
  proposal: Proposal | null;
}>({
  isOpen: false,
  proposal: null,
});
```

## Uso no Código

### Abrir Modal para Criar
```typescript
const handleCreate = () => {
  setModalState({ isOpen: true, proposal: null });
};
```

### Abrir Modal para Editar
```typescript
const handleEdit = (proposal: Proposal) => {
  setModalState({ isOpen: true, proposal });
};
```

### Submit Handler
```typescript
const handleFormSubmit = (data: ProposalFormData) => {
  if (modalState.proposal) {
    // Update existing proposal
    updateMutation.mutate({ id: modalState.proposal.id, data });
  } else {
    // Create new proposal
    createMutation.mutate(data as CreateProposalData);
  }
};
```

### Renderizar Modal
```typescript
<ProposalFormModal
  isOpen={modalState.isOpen}
  onClose={() => setModalState({ isOpen: false, proposal: null })}
  onSubmit={handleFormSubmit}
  proposal={modalState.proposal}
  isLoading={createMutation.isPending || updateMutation.isPending}
  customers={customersData?.data || []}
  products={productsData?.data || []}
  loadingCustomers={loadingCustomers}
  loadingProducts={loadingProducts}
/>
```

## Detalhes Técnicos

### React Hook Form
- **useForm**: Gestão do formulário principal
- **useFieldArray**: Gestão do array dinâmico de itens
- **useWatch**: Watch otimizado compatível com React Compiler
- **Controller**: Para campos controlados (selects)
- **zodResolver**: Integração com Zod para validação

### Performance
- **useMemo**: Cálculos de totais memoizados
- **useWatch**: Ao invés de `watch()` para compatibilidade com React Compiler
- **setValue**: Auto-preenchimento de preço sem re-render desnecessário

### Defaults Inteligentes
- **Data de emissão**: Data atual
- **Data de validade**: 30 dias a partir de hoje
- **Status**: 'draft'
- **1 item inicial**: Com valores zerados

## Arquivos Criados

1. **ProposalFormModal.tsx** (573 linhas)
   - Componente modal completo
   - Gestão de items com useFieldArray
   - Cálculos em tempo real
   - Auto-preenchimento de preço

## Arquivos Modificados

1. **ProposalListPage.tsx**
   - Imports: ProposalFormModal, customerService, productService
   - State: modalState para controlar abertura/fechamento
   - Queries: customers e products
   - Mutations: createMutation e updateMutation
   - Handlers: handleCreate, handleEdit, handleFormSubmit
   - Render: <ProposalFormModal /> no final

## Testes Realizados

✅ **Build**: Frontend compilado com sucesso (11.83s)
✅ **TypeScript**: 0 erros de tipo
✅ **Linting**: Nenhum problema detectado
✅ **React Compiler**: Compatível (usando useWatch ao invés de watch)

## Próximos Passos Sugeridos

### Melhorias de UX
- [ ] Adicionar confirmação ao fechar modal com dados não salvos
- [ ] Adicionar busca/filtro nos dropdowns de cliente e produto
- [ ] Adicionar preview de PDF antes de criar proposta

### Features Adicionais
- [ ] Duplicar item existente
- [ ] Importar itens de proposta anterior
- [ ] Templates de proposta
- [ ] Cálculo automático de impostos
- [ ] Suporte a múltiplas moedas

### Integração com Opportunities
- [ ] Campo opportunity_id (quando Module 4 for implementado)
- [ ] Auto-preencher itens ao selecionar oportunidade
- [ ] Link visual para a oportunidade relacionada

## Observações

- O componente segue o padrão dos modais existentes (CustomerFormModal, ProductFormModal)
- Usa os mesmos componentes base (Button, Input, Alert)
- Implementa todas as validações do backend no frontend
- Totalmente responsivo e acessível
- Pronto para produção
