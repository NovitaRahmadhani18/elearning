import { Card, CardContent } from '@/components/ui/card';
import { Icon } from '@/components/ui/icon';
import { LucideIcon } from 'lucide-react';

interface AdminDashboardCardProps {
    title: string;
    value: string;
    icon?: LucideIcon | null;
    [key: string]: unknown;
}

const AdminDashboardCard: React.FC<AdminDashboardCardProps> = ({
    title,
    value,
    icon,
}) => {
    return (
        <Card className="shadow-none">
            <CardContent className="flex items-center">
                <div className="mr-4">
                    <div className="bg-secondary rounded-lg p-3">
                        {icon && (
                            <Icon iconNode={icon} className="h-6 w-6 text-white" />
                        )}
                    </div>
                </div>

                <div>
                    <p className="text-sm text-gray-600">{title}</p>
                    <p className="text-2xl font-bold text-gray-800">{value}</p>
                </div>
            </CardContent>
        </Card>
    );
};

export default AdminDashboardCard;
