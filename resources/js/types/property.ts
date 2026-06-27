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

export type PropertyVerification = {
    id: number;
    status: StatusBadge;
    score: number;
    checks: VerificationCheck[];
    extracted: Record<string, unknown> | null;
    notes: string | null;
    document_id: number | null;
    run_by?: string | null;
    created_at: string | null;
};

export type PropertyDocument = {
    id: number;
    type: LabelledValue;
    original_name: string;
    mime_type: string;
    size: number;
    size_label: string;
    is_image: boolean;
    file_hash: string;
    download_url: string;
    created_at: string | null;
};

export type PropertyBlock = {
    sequence: number;
    hash: string;
    previous_hash: string;
    created_at: string | null;
};

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
    registered_by?: { id: number; name: string };
    verified_at: string | null;
    created_at: string | null;
    documents?: PropertyDocument[];
    latest_verification?: PropertyVerification | null;
    verifications?: PropertyVerification[];
    block?: PropertyBlock | null;
};

export type ChainReport = {
    intact: boolean;
    blocks: number;
    broken_at_sequence: number | null;
    issues: string[];
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
