<template>
    <Head title="Create Contribution" />
    <AdminLayout>
        <div class="p-6 max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight">New Contribution</h1>
                <button
                    type="button"
                    @click="$inertia.visit(route('contributions.index'))"
                    class="inline-flex items-center rounded-lg border border-muted-foreground/20 
                           bg-background px-4 py-2 text-sm font-medium text-muted-foreground 
                           shadow-sm hover:bg-muted/10 focus:outline-none focus:ring-2 
                           focus:ring-primary focus:ring-offset-2 transition"
                >
                    ← Back
                </button>
            </div>

            <!-- Form -->
            <form 
                class="bg-card rounded-2xl border shadow-md p-8 flex flex-col gap-8" 
                @submit.prevent="submit"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left column -->
                    <div class="flex flex-col gap-6">
                        <!-- Contribution Type (enum) -->
                        <div>
                            <label for="contribution_type" class="block text-sm font-medium text-muted-foreground mb-2">
                                Contribution Type
                            </label>
                            <select 
                                v-model="form.contribution_type" 
                                id="contribution_type" 
                                required
                                class="block w-full rounded-lg border border-muted-foreground/20 bg-background 
                                       px-4 py-2.5 text-sm shadow-sm focus:border-primary 
                                       focus:ring-2 focus:ring-primary/20 focus:outline-none transition"
                            >
                                <option value="">Select type</option>
                                <option value="community">Community</option>
                                <option value="personal">Personal</option>
                            </select>
                        </div>

                        <!-- User ID (only show if personal) -->
                        <div v-if="form.contribution_type === 'personal'">
                            <label for="user_id" class="block text-sm font-medium text-muted-foreground mb-2">
                                Contributor
                            </label>
                            <select
                                v-model="form.user_id"
                                id="user_id"
                                required
                                class="block w-full rounded-lg border border-muted-foreground/20 bg-background 
                                       px-4 py-2.5 text-sm shadow-sm focus:border-primary 
                                       focus:ring-2 focus:ring-primary/20 focus:outline-none transition"
                            >
                                <option value="">Select User</option>
                                <option 
                                    v-for="user in users" 
                                    :key="user.id" 
                                    :value="user.id"
                                >
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-muted-foreground mb-2">
                                Amount (₱)
                            </label>
                            <input 
                                v-model.number="form.amount" 
                                id="amount" 
                                type="number" 
                                min="0" 
                                step="0.01" 
                                required
                                class="block w-full rounded-lg border border-muted-foreground/20 bg-background 
                                       px-4 py-2.5 text-sm shadow-sm focus:border-primary 
                                       focus:ring-2 focus:ring-primary/20 focus:outline-none transition" 
                                placeholder="Enter amount" 
                            />
                        </div>
                    </div>

                    <!-- Right column -->
                    <div class="flex flex-col gap-6">
                        <!-- Contribution Date -->
                        <div>
                            <label for="contribution_date" class="block text-sm font-medium text-muted-foreground mb-2">
                                Contribution Date
                            </label>
                            <input 
                                v-model="form.contribution_date" 
                                id="contribution_date" 
                                type="date" 
                                required
                                class="block w-full rounded-lg border border-muted-foreground/20 bg-background 
                                       px-4 py-2.5 text-sm shadow-sm focus:border-primary 
                                       focus:ring-2 focus:ring-primary/20 focus:outline-none transition" 
                            />
                        </div>

                        <!-- Status (enum) -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-muted-foreground mb-2">
                                Status
                            </label>
                            <select 
                                v-model="form.status" 
                                id="status" 
                                required
                                class="block w-full rounded-lg border border-muted-foreground/20 bg-background 
                                       px-4 py-2.5 text-sm shadow-sm focus:border-primary 
                                       focus:ring-2 focus:ring-primary/20 focus:outline-none transition"
                            >
                                <option value="">Select status</option>
                                <option value="active">Active</option>
                                <option value="disabled">Disabled</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-muted-foreground mb-2">
                                Description / Notes
                            </label>
                            <textarea 
                                v-model="form.description" 
                                id="description" 
                                rows="4"
                                class="block w-full rounded-lg border border-muted-foreground/20 bg-background 
                                       px-4 py-2.5 text-sm shadow-sm focus:border-primary 
                                       focus:ring-2 focus:ring-primary/20 focus:outline-none transition" 
                                placeholder="Add details about the contribution..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button 
                        type="submit"
                        class="inline-flex items-center rounded-lg bg-primary px-8 py-3 
                               text-base font-semibold text-black shadow hover:bg-primary/90 
                               focus:outline-none focus:ring-2 focus:ring-primary 
                               focus:ring-offset-2 transition"
                    >
                        Save Contribution
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import AdminLayout from "@/layouts/AdminLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";

const form = useForm({
    user_id: "",
    amount: "",
    contribution_date: "",
    contribution_type: "",
    status: "active",
    description: "",
});
const search = ref({});
const users = ref([]);

const fetchList = async () => {
    const route_url = route("admin.users.list");
    try {
        const response = await axios.get(route_url, { params: search.value });
        if(response.data.result === true){
            users.value = response.data.data;
        }
    } catch (error) {
        console.error("Error fetching users:", error);
    }
};

onMounted(() => {
    fetchList();
});

watch(search, () => {
    fetchList();
}, { deep: true });

const submit = async () =>{
    const route_url = route('contributions.store');
    try{
        const response = await axios.post(route_url, form);
        if(response.data.result === true){
            console.log('success');
            // reset fields correctly
            form.user_id = "";
            form.amount = "";
            form.contribution_date = "";
            form.contribution_type = "";
            form.status = "active";
            form.description = "";
        }else{
            console.log("Something went wrong:", response.data.message);
        }
    }catch(error){
        if (error.response && error.response.data.errors) {
            console.log("Validation errors:", error.response.data.errors);
        } else {
            console.log("Unexpected error:", error);
        }
    }
}
</script>
