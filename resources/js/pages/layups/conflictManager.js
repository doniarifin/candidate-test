export default function conflictManager() {
    return {
        open: false,
        selected: 0,

        conflicts: [
            {
                id: 1,
                name: "CLT-5-150-L",
                issue: "Thickness mismatch",
                existing: [
                    { thickness: 40, width: 150, angle: 0 },
                    { thickness: 30, width: 150, angle: 90 },
                ],
                importing: [
                    { thickness: 40, width: 150, angle: 0 },
                    { thickness: 35, width: 150, angle: 90 },
                ],
            },
        ],

        get current() {
            return this.conflicts[this.selected] || {};
        },

        select(i) {
            this.selected = i;
        },

        next() {
            if (this.selected < this.conflicts.length - 1) {
                this.selected++;
            }
        },

        keepExisting() {
            console.log("Keep existing", this.current.id);
        },

        acceptNew() {
            console.log("Accept new", this.current.id);
        },

        isDifferent(index, row) {
            const existing = this.current.existing[index];
            if (!existing) return false;

            return (
                existing.thickness !== row.thickness ||
                existing.width !== row.width ||
                existing.angle !== row.angle
            );
        },
    };
}
