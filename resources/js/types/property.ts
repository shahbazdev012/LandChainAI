export type StatusBadge = {
    value: string;
    label: string;
    color: string;
};

export type EnumOption = {
    value: string;
    label: string;
};

export type LabelledValue = {
    value: string;
    label: string;
};

export type VerificationCheck = {
    key: string;
    label: string;
    passed: boolean;
    message: string;
    points: number;
    max_points: number;
};

export type AiCrossCheck = {
    provider: string;
    match: boolean | null;
    confidence: number | null;
    issues: string[];
    notes: string | null;
};

export type PropertyBlock = {
    sequence: number;
    hash: string;
    previous_hash: string;
    created_at: string | null;
};

/** Staff-side property record (ground truth). */
export type Property = {
    id: number;
    property_number: string;
    title: string;
    type: LabelledValue;
    description: string | null;
    owner_name: string;
    owner_cnic: string;
    owner_contact: string | null;
    address: string;
    city: string;
    province: string;
    area_value: string;
    area_unit: LabelledValue;
    area_label: string;
    status: StatusBadge;
    created_by?: string;
    approved_by?: string | null;
    approved_at: string | null;
    created_at: string | null;
    block?: PropertyBlock | null;
};

/** Public search result (masked, approved only). */
export type PublicPropertySummary = {
    id: number;
    property_number: string;
    title: string;
    type: string;
    owner_name: string;
    owner_cnic: string;
    city: string;
    province: string;
};

/** Public detail projection. */
export type PublicProperty = PublicPropertySummary & {
    address: string;
    area_label: string;
    approved_at: string | null;
};

/** Public verification result shown to the user after upload. */
export type VerificationResult = {
    final_status: StatusBadge;
    score: number | null;
    checks: VerificationCheck[];
    notes: string | null;
    ai: AiCrossCheck | null;
    created_at: string | null;
};

export type ChainReport = {
    intact: boolean;
    blocks: number;
    broken_at_sequence: number | null;
    issues: string[];
};

export type PropertyPermissions = {
    update: boolean;
    approve: boolean;
    delete: boolean;
};

export type Paginated<T> = {
    data: T[];
    meta: {
        current_page: number;
        from: number | null;
        to: number | null;
        last_page: number;
        per_page: number;
        total: number;
        path: string;
    };
};
